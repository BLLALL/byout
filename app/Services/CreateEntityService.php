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

    abstract protected function getImageColumn();

    abstract protected function getImagePath();

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

    protected function setOwnerAndCurrency($entity, Request $request)
    {
        if (method_exists($entity, 'owner')) {
            $userId = $request->input('owner_id');
            $owner = Owner::where('user_id', $userId)->first();
            $entity->owner_id = $owner->id;
        } elseif (method_exists($entity, 'hotel')) {
            $owner = $entity->hotel->owner;
        } else {
            throw new \Exception('Entity does not have an owner or hotel relation');
        }

        if ($entity->price) {
            $money = Money::of($request->price, $owner->user->preferred_currency);
            $entity->price = $money->getMinorAmount()->toInt();
            $entity->currency = $owner->user->preferred_currency;
        }


    }

    protected function handleImages($entity, $request)
    {
        if ($request->hasFile($this->imageColumn)) {
            $images = [];
            foreach ($request->file($this->imageColumn) as $image) {
                $images[] = 'https://fayroz97.com/real-estate/' . $image->store($this->imagePath, 'public');
            }
        }

        $entity->{$this->imageColumn} = $images;
        return $entity;
    }

    protected function handleDocuments($entity, Request $request)
    {
        if($request->has('documents')) {
            foreach ($request->documents as $document) {
                return $request->documents;
                $path =  'https://fayroz97.com/real-estate/' . $document['file']->store('documents/' . class_basename($entity), 'public');
                $entity->documents()->create([
                    'document_type' => $document['type'],
                    'file_path' => $path,
                    'owner_id' => $entity->owner_id,
                ]);
            }
        }
    }



}
