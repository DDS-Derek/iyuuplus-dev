<?php

namespace app\admin\controller;

use app\common\HasBackupRecovery;
use app\common\HasDelete;
use app\model\Client;
use plugin\admin\app\common\Auth;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 下载器设置
 */
class ClientController extends Crud
{
    use HasDelete, HasBackupRecovery;

    /**
     * 无需登录及鉴权的方法
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * @var Client
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new Client;
    }

    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('client/index');
    }

    /**
     * 插入
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response
    {
        if ($request->method() === 'POST') {
            $response = parent::insert($request);
            Client::backupToJson($this->model);
            return $response;
        }
        return view('client/save');
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update(Request $request): Response
    {
        if ($request->method() === 'POST') {
            [$id, $data] = $this->updateInput($request);
            /** @var Client $model */
            $model = $this->model->find($id);
            $before = $model->is_default;
            foreach ($data as $key => $val) {
                $model->{$key} = $val;
            }
            $after = $model->is_default;
            if ($before && !$after) {
                return $this->fail('必须有一个默认下载器');
            }

            $model->save();
            Client::backupToJson($this->model);
            return $this->json(0);
        }
        return view('client/save');
    }

    /**
     * 插入前置方法
     * @param Request $request
     * @return array
     * @throws BusinessException
     */
    protected function insertInput(Request $request): array
    {
        $data = $this->inputFilter($request->post());
        if (!Auth::isSupperAdmin() && $this->dataLimit) {
            if (!empty($data[$this->dataLimitField])) {
                $admin_id = $data[$this->dataLimitField];
                if (!in_array($admin_id, Auth::getScopeAdminIds(true))) {
                    throw new BusinessException('无数据权限');
                }
            }
        }
        return $data;
    }

    /**
     * 更新前置方法
     * @param Request $request
     * @return array
     * @throws BusinessException
     */
    protected function updateInput(Request $request): array
    {
        $primary_key = $this->model->getKeyName();
        $id = $request->post($primary_key);
        $data = $this->inputFilter($request->post());
        $model = $this->model->find($id);
        if (!$model) {
            throw new BusinessException('记录不存在', 2);
        }
        if (!Auth::isSupperAdmin() && $this->dataLimit) {
            $scopeAdminIds = Auth::getScopeAdminIds(true);
            $admin_ids = [
                $data[$this->dataLimitField] ?? false, // 检查要更新的数据admin_id是否是有权限的值
                $model->{$this->dataLimitField} ?? false // 检查要更新的记录的admin_id是否有权限
            ];
            foreach ($admin_ids as $admin_id) {
                if ($admin_id && !in_array($admin_id, $scopeAdminIds)) {
                    throw new BusinessException('无数据权限');
                }
            }
        }
        $password_filed = 'password';
        if (isset($data[$password_filed])) {
            // 密码为空，则不更新密码
            if ($data[$password_filed] === '') {
                unset($data[$password_filed]);
            }
        }
        unset($data[$primary_key]);
        return [$id, $data];
    }
}
