<?php

namespace hhttp\io\gateway;

use hhttp\io\http\Resources\InnerResource;
use hhttp\io\gateway\HttpService;
use Illuminate\Http\Request;

class GatewayController extends BaseController
{
    /**
     * 服务代理
     * @param Request $request
     * @param \hoo\io\gateway\HttpService $HttpService
     * @return InnerResource
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \hoo\io\common\Exceptions\HooException
     */
    public function gateway(Request $request, HttpService $HttpService): InnerResource
    {
        $data = $HttpService->gateway($request);
        return new InnerResource($data);
    }
}
