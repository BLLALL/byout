<?php

namespace App\Services;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brick\Money\Money;
use Illuminate\Support\Facades\Log;
abstract class CreateEntityService
{
    protected $model;
    protected $fillableAttributes;
    protected $imageColumn;
    protected $imagePath;

    abstract protected function getModel();

    abstract protected function getFillableAttributes();

    protected function getImageColumn() {
        return null;
    }

    protected function getImagePath() {
        return null;
    }

    public function createEntity(Request $request)
    {
        $this->initializeAttributes();

        return DB::transaction(function () use ($request) {
            $entity = $this->fillEntityAttributes($request);
            $this->setOwnerAndCurrency($entity, $request);
            $this->handleImages($entity, $request);
            $entity->save();
            $this->handleDocuments($entity, $request);

            if (method_exists($this, 'handleAdditionalData')) {
                $this->handleAdditionalData($entity, $request->validated());
            }
            return $entity;
        });
    }

    protected function initializeAttributes()
    {
        $this->model = $this->getModel();
        $this->fillableAttributes = $this->getFillableAttributes();
        $this->imageColumn = $this->getImageColumn();
        $this->imagePath = $this->getImagePath();
    }

    protected function fillEntityAttributes(Request $request)
    {
        return $this->model->fill($request->only($this->fillableAttributes));
    }

    public function setOwner($entity, Request $request)
    {
        if (method_exists($entity, 'owner')) {
            $userId = $request->input('owner_id');
            $owner = Owner::where('user_id', $userId)->first();
            $entity->owner_id = $owner->id;
            return $owner;
        } elseif (method_exists($entity, 'hotel')) {
            return $entity->hotel->owner;
        } else {
            throw new \Exception('Entity does not have an owner or hotel relation');
        }
    }

    public function setCurrency($entity, Request $request, $owner)
    {
        if ($entity->price) {
            $money = Money::of($request->price, $owner->user->preferred_currency);
            $entity->price = $money->getMinorAmount()->toInt();
            $entity->currency = $owner->user->preferred_currency;
        }
    }

    protected function setOwnerAndCurrency($entity, Request $request)
    {
        
        $owner = $this->setOwner($entity, $request);

        $this->setCurrency($entity, $request, $owner);
    }


    protected function handleImages($entity, $request)
    {
        if ($request->hasFile($this->imageColumn)) {
            $images = [];
            foreach ($request->file($this->imageColumn) as $image) {
                $images[] = 'https://travelersres.com/' . $image->store($this->imagePath, 'public');
            }
        }

        $entity->{$this->imageColumn} = $images;
        return $entity;
    }

    public function handleDocuments($entity, Request $request)
    {
        if($request->has('documents')) {
            foreach ($request->documents as $document) {
                $path =  'https://travelersres.com/' . $document['file']->store('documents/' . class_basename($entity), 'public');
                $entity->documents()->create([
                    'document_type' => $document['type'],
                    'file_path' => $path,
                    'owner_id' => $entity->owner_id,
                ]);
            }
        }
    }

}
