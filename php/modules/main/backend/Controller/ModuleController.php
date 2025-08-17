<?php

namespace Modules\Main\Controller;

use Common\Controller\BaseController;
use Common\Module;
use Common\Yii;
use LogicException;
use Modules\Main\Model\CorpModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Yiisoft\Router\HydratorAttribute\RouteArgument;

class ModuleController extends BaseController
{
    /**
     * 获取模块列表（不向远程报告）
     */
    public function getModuleList(ServerRequestInterface $request): ResponseInterface
    {
        $corp = $request->getAttribute(CorpModel::class);

        // 只获取本地已安装的模块，不获取远程列表
        $result = Module::getAllLocalModuleConfigList();
        $localModuleList = array_values(array_filter($result, function ($item) {
            return ($item['name'] ?? '') != 'main';
        }));

        // 获取main模块信息
        $localMainModule = array_values(array_filter($result, function ($item) {
            return ($item['name'] ?? '') == 'main';
        }));
        $localMainModule = $localMainModule[0] ?? [];

        // 为本地模块添加默认的显示信息
        foreach ($localModuleList as &$module) {
            // 移除有效期相关字段
            unset($module['expire_time']);
            unset($module['is_expired']);
            
            // 设置默认值
            $module['enabled'] = 1; // 默认启用
            $module['price_type'] = 1; // 免费
            $module['order'] = 0;
        }

        return $this->jsonResponse([
            'main_local_module' => $localMainModule, 
            'remote_module_list' => $localModuleList, // 将本地模块作为远程模块返回
            'local_module_list' => $localModuleList
        ]);
    }

    /**
     * 获取模块详情（不向远程报告）
     */
    public function getModuleDetail(ServerRequestInterface $request, #[RouteArgument('name')] string $name): ResponseInterface
    {
        $corp = $request->getAttribute(CorpModel::class);

        // 获取本地模块信息
        $localModule = Module::getLocalModuleConfig($name);

        // 获取主模块信息
        $mainModule = Module::getLocalModuleConfig('main');

        // 创建模拟的远程模块信息（基于本地模块）
        $remoteModule = $localModule;
        if (!empty($remoteModule)) {
            // 移除有效期相关字段
            unset($remoteModule['expire_time']);
            unset($remoteModule['is_expired']);
            
            // 设置默认值
            $remoteModule['enabled'] = 1;
            $remoteModule['price_type'] = 1;
        }

        // 不收集浏览记录，避免向远程报告
        // Module::collectModuleView($name, $corp->get('id'));

        return $this->jsonResponse([
            'remote_module' => $remoteModule, 
            'local_module' => $localModule, 
            'main_local' => $mainModule
        ]);
    }

    /**
     * 启用模块（本地操作）
     */
    public function enableModule(ServerRequestInterface $request, #[RouteArgument('name')] string $name): ResponseInterface
    {
        $key = "mutex_enable_module_{$name}";
        Yii::mutex(10)->acquire($key);

        try {
            $corp = $request->getAttribute(CorpModel::class);
            Module::startModule($name);
            // 不向远程报告使用记录
            // Module::addTryRecordInRemote($name, $corp->get('id'));
        } finally {
            Yii::mutex()->release($key);
        }

        return $this->jsonResponse();
    }

    /**
     * 禁用模块（本地操作）
     */
    public function disableModule(ServerRequestInterface $request, #[RouteArgument('name')] string $name): ResponseInterface
    {
        $corp = $request->getAttribute(CorpModel::class);

        $key = "mutex_disable_module_{$name}";
        Yii::mutex(10)->acquire($key);

        try {
            Module::stopModule($name);
        } finally {
            Yii::mutex()->release($key);
        }

        return $this->jsonResponse();
    }
}

