<?php
declare (strict_types = 1);
namespace app\service;
class OrderService{

    public function userAddress($address){

    }

    public function addOrder($uid,$address,$remark,$items,$shop){
        $XA1 = uniqid("");//事务id，为XA事务指定一个id，xid 必须是一个唯一值。
        $XA2 = uniqid("");
        $XA3 = uniqid("");
        $this->Start($XA1,$XA2,$XA3);
        try {
            if (!$UserAddressRestful = $this->userAddress($address)) {
                return ["status" => 1,"message" => '修改地址回收错误！'];
            }
            if (!$OrderRestful = $this->addOrders($uid,$address,$remark)) {
                return ["status" => 1,"message" => '订单新增数据错误！'];
            }

            $totalAmount = 0;
            foreach ($items as $data) {
                //查询商品sku
                $ProductSkuData = [
                    'table' => 'lms_product_skus',
                    'field' => 'id,price,product_id,stock',
                    'where' => 'id = '.$data['sku_id']
                ];

                $ProductSkuRestful = $this->db2->getOne($ProductSkuData);

                //拆分订单到订单详情
                $OrderItemData = [
                    'table' => 'lms_order_items'.$this->Order['random'],
                    'data' => [
                        'order_id' => $this->Order["id"],
                        'product_id' => $ProductSkuRestful['product_id'],
                        'product_sku_id' => $data['sku_id'],
                        'amount' => $data["amount"],
                        'price' => $ProductSkuRestful['price']
                    ]
                ];

                $OrderItemRestful = $this->db3->add($OrderItemData);
                if (!in_array($OrderItemRestful,['0','1'])) {
                    throw new InternalException("订单详情新增错误！");
                }

                $totalAmount += $ProductSkuRestful['price'] * $data['amount'];

                //修改sku库存
                if (!$ProductSkuRestful <= $data['amount']) {
                    $stock_amount = $ProductSkuRestful['stock'] - $data['amount'];
                    $StockData = [
                        'table' => 'lms_product_skus',
                        'data' => [
                            'stock' => $stock_amount
                        ],
                        'where' => 'id = '.$data['sku_id']
                    ];
                    $StockRestful = $this->db2->update($StockData);
                    if (!in_array($StockRestful,['0','1'])) {
                        throw new InternalException("更新库存错误！");
                    }
                }else{
                    throw new InvalidRequestException('该商品库存不足');
                }
            }

            //更新订单金额
            $totalAmountData = [
                'table' => 'lms_orders'.$this->Order["random"],
                'data' => [
                    'total_amount' => $totalAmount
                ],
                'where' => 'id = '.$this->Order["id"]
            ];

            $OrderAmountRestful = $this->db3->update($totalAmountData);
            if (!in_array($OrderAmountRestful,['0','1'])) {
                throw new InternalException("修改订单金额错误！");
            }

            $UserOrderRestful = $this->UserOrder($user);
//      $ShopOrderRestful = $this->ShopOrder($shop);

            //阶段1：分布式事务准备提交
            $this->End($XA1,$XA2,$XA3);
            $this->Prepare($XA1,$XA2,$XA3);
            //阶段2：分布式事务准备提交
            $this->Commit($XA1,$XA2,$XA3);
        } catch (\Exception $e) {
            //阶段1：分布式事务准备回滚
            $this->End($XA1,$XA2,$XA3);
            $this->Prepare($XA1,$XA2,$XA3);
            //阶段2：分布式事务准备回滚
            $this->Commit($XA1,$XA2,$XA3);
            die("Exception:".$e->getMessage());
        }
        return ['id' => $this->Order['id'],'table' => 'lms_orders'.$this->Order['random']];
    }
    public function addOrders($user,$address,$remark='',$total_amount = 0)
    {
        $OrderData = [
            'table' => 'lms_orders'.$this->Order['random'],
            'data' => [
                'id' => $this->Order['id'],
                'no' => $this->findAvailableNo(),
                'user_id' => $user->id,
                'address' => json_encode([
                    "address"        => $address->full_address,
                    "zip"            => $address->zip,
                    "contact_name"   => $address->contact_name,
                    "contact_phone"  => $address->contact_phone,
                ]),
                'total_amount' => $total_amount,
                'remark' => $remark,
                'status' => 0,
                'type' => Order::TYPE_SECKILL
            ]
        ];
        $OrderRestful = $this->db3->add($OrderData);
        if (!in_array($OrderRestful,['0','1'])) {
            throw new InternalException("订单新增数据错误！");
        }else{
            return true;
        }
    }

}
