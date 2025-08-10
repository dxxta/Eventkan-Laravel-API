<?php

namespace App\Http\Traits;

use App\Models\Audit;
use Illuminate\Support\Facades\Auth;

trait AuditTrait
{
    public static function createAudit($model)
    {
        $oldData = $model->getOriginal();
        $note = $model->auditNote ?? $model->name . ' ' . ($model->deleted_at ? 'deleted' : 'updated');

        if (isset($model->auditNote)) {
            unset($model->auditNote);
        }

        $audit = new Audit();
        $audit->user_id = Auth::user()->id;
        $audit->entity_id = $model->id;
        $audit->entity_name = $model->name;
        $audit->field_name = array_keys($oldData);
        $audit->field_value = array_values($oldData);
        $audit->action = $model->deleted_at ? 'delete' : 'update';
        $audit->note = $note;
        $audit->save();
    }
}
