<?php

namespace hhttp\io\monitor\hm\Request;

use Illuminate\Support\Str;

class LogicalPipelinesRequest extends BaseRequest
{
    public function rules()
    {
        $action_name = Str::after($this->route()->getActionName(), '@');

        $action_name = $action_name.":".$this->method();

        switch ($action_name) {
            case 'save:POST':
                $rules = [
                    'rec_subject_id' => 'bail|required',
                    'name' => 'bail|required',
                    'group' => 'bail|required',
                ];
                break;
            case 'delete:POST':
                $rules = [
                    'id' => 'bail|required',
                ];
                break;
            case 'run:POST':
                $rules = [
                    'id' => 'bail|required',
                ];
                break;
            case 'arrange:GET':
                $rules = [
                    'id' => 'bail|required',
                ];
                break;
            case 'arrangeEdit:POST':
                $rules = [
                    'arrange_id' => 'bail|required',
                    'logical_block' => 'bail|required',
                    'name' => 'bail|required',
                ];
                break;
            case 'addArrangeItem:GET':
                $rules = [
                    'pipeline_id' => 'bail|required',
                    'arrange_id' => 'bail|required',
                ];
                break;
            case 'addArrangeItem:POST':
                $rules = [
                    'pipeline_id' => 'bail|required',
                    'arrange_id' => 'bail|required',
                    'op' => 'bail|required',
                    'type' => 'bail|required',
                ];
                break;
            case 'arrangeDelete:POST':
                $rules = [
                    'pipeline_id' => 'bail|required',
                    'arrange_id' => 'bail|required',
                ];
                break;
            default:
                $rules = [];
                break;
        }
        return $rules;
    }
}
