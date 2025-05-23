<?php

namespace hhttp\io\monitor\hm\Request;

use Illuminate\Support\Str;

class LogicalBlockRequest extends BaseRequest
{
    public function rules()
    {
        $action_name = Str::after($this->route()->getActionName(), '@');

        $action_name = $action_name.":".$this->method();

        switch ($action_name) {
            case 'save:POST':
                $rules = [
                    'name' => 'bail|required',
                    'group' => 'bail|required',
                    'label' => 'bail|required',
                    'logical_block' => 'bail|required',
                ];
                break;
            case 'paste:POST':
                $rules = [
                    'logical_block' => 'bail|required',
                ];
                break;
            case 'detail:GET':
                $rules = [
                    'id' => 'bail|required',
                ];
                break;
            case 'copyNew:POST':
                $rules = [
                    'id' => 'bail|required',
                ];
                break;
            case 'copy:GET':
                $rules = [
                    'id' => 'bail|required',
                ];
                break;
            case 'delete:POST':
                $rules = [
                    'id' => 'bail|required',
                ];
                break;
            case 'run:POST':
                $rules = [
                    'logical_block' => 'bail|required',
                ];
                break;
            default:
                $rules = [];
                break;
        }
        return $rules;
    }
}
