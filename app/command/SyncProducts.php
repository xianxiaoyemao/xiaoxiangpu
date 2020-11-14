<?php
declare (strict_types = 1);

namespace app\command;

use app\common\SearchBuilders\SearchBuilders;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\common\model\Product;
use think\facade\Db;
class SyncProducts extends Command
{
    protected $description = '将商品数据同步到 Elasticsearch';
    protected function configure()
    {
        // 指令配置
        $this->setName('syncproducts')
            ->setDescription('同步商品命令');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        Product::with(['skus'])-> chunk(100, function ($products){
            $this->writeln(sprintf('正在同步 ID 范围为 %s 至 %s 的商品', $products->first()->id, $products->last()->id));
            // 遍历商品
            foreach ($products as $product) {
                // 将商品模型转为 Elasticsearch 所用的数组
                $data = $product->toESArray();
                (new SearchBuilders) -> add_doc('products',$data['id'],$data);
            }

        });
        $this -> writeln('同步完成');
    }

    public function writeln($msg){
        $output = new Output();
        return $output->writeln($msg);
    }
}
