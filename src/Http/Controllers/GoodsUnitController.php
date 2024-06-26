<?php

namespace ManoCode\Erp\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Slowlyo\OwlAdmin\Renderers\Page;
use Slowlyo\OwlAdmin\Renderers\Form;
use Slowlyo\OwlAdmin\Controllers\AdminController;
use ManoCode\Erp\Models\GoodsUnit;
use ManoCode\Erp\Services\GoodsUnitService;

/**
 * 计量单位
 *
 * @property GoodsUnitService $service
 */
class GoodsUnitController extends AdminController
{
    protected string $serviceName = GoodsUnitService::class;

    /**
     * 获取商品单位
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function getGoodsUnit(): \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
    {
        $lists = GoodsUnit::query()->where(['status'=>1])->select([
            DB::raw('name as label'),
            DB::raw('id as value'),
        ])->get();

        return $this->response()->success($lists);
    }
    public function list(): Page
    {
        $crud = $this->baseCRUD()
            ->filterTogglable(false)
			->headerToolbar([
				$this->createButton(true),
				...$this->baseHeaderToolBar()
			])
            ->columns([
                amis()->TableColumn('id', 'ID')->sortable(),
				amis()->TableColumn('name', '名称'),
                amis()->TableColumn('status', '状态')->quickEdit(
                    amis()->SwitchControl()->mode('inline')->saveImmediately(true)
                ),
				amis()->TableColumn('created_at', __('admin.created_at'))->set('type', 'datetime')->sortable(),
				amis()->TableColumn('updated_at', __('admin.updated_at'))->set('type', 'datetime')->sortable(),
                $this->rowActions(true)
            ]);

        return $this->baseList($crud);
    }

    /**
     * 操作列
     *
     * @param bool   $dialog
     * @param string $dialogSize
     *
     * @return \Slowlyo\OwlAdmin\Renderers\Operation
     */
    protected function rowActions(bool|array $dialog = false, string $dialogSize = '')
    {
        if (is_array($dialog)) {
            return amis()->Operation()->label(__('admin.actions'))->buttons($dialog);
        }

        return amis()->Operation()->label(__('admin.actions'))->buttons([
//            $this->rowShowButton($dialog, $dialogSize),
            $this->rowEditButton($dialog, $dialogSize),
            $this->rowDeleteButton(),
        ]);
    }

    public function form($isEdit = false): Form
    {
        return $this->baseForm()->body([
            amis()->TextControl('name', '名称')->maxLength(15)->required(),
            amis()->SwitchControl('status', '是否启用')->trueValue(1)->falseValue(0)->required()
                ->onText('启用')
                ->offText('禁用')
                ->value(1),
			amis()->NumberControl('sort', '排序')->min(0)->value(0)->required(),
        ]);
    }

    public function detail(): Form
    {
        return $this->baseDetail()->body([
            amis()->TextControl('id', 'ID')->static(),
			amis()->TextControl('name', '名称')->static(),
            amis()->SwitchControl('status', '是否启用')->trueValue(1)->falseValue(0)
                ->onText('启用')
                ->offText('禁用')
                ->static(),
			amis()->TextControl('sort', '排序')->static(),
			amis()->TextControl('created_at', __('admin.created_at'))->static(),
			amis()->TextControl('updated_at', __('admin.updated_at'))->static()
        ]);
    }
}
