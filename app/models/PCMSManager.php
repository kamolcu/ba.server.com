<?php
class PCMSManager
{
    public $paymentChannels = array(
        '1' => 'Credit Card',
        '2' => 'Ewallet',
        '3' => 'Installment',
        '4' => 'ATM',
        '5' => 'iBanking',
        '6' => 'Bank Counter',
        '7' => 'COD',
        '8' => 'Counter Service',
    );
    public function updateData($model, $data, $datasetId) {
        foreach ($data as $name => $count) {
            $row_exists = App::make('Helper')->isCompletedOrderExists($name, $datasetId);
            if (!$row_exists) {
                $inputs = array(
                    'name' => $name,
                    'count' => $count,
                    'dataset_id' => $datasetId,
                );
                $rules = array();
                App::make('Helper')->create($model, $inputs, $rules);
            }
        }
    }
    public function getPaidOrder($start_date, $end_date) {
        try {
            $orders = $this->getOrderDataByPeriod($start_date, $end_date);
            // Classify each payment channels
            $dataSet = array();
            foreach ($orders as $order) {
                if ($order['order_status'] == 'new') {
                    $name = $this->paymentChannels[$order['payment_method']];
                    if (!isset($dataSet[$name])) {
                        $dataSet[$name] = 1;
                    } else {
                        $dataSet[$name] = $dataSet[$name] + 1;
                    }
                }
            }
            //s($dataSet);
            return $dataSet;
        }
        catch(Exception $ex) {
            $msg = sprintf('Exception = %s', $ex->getMessage());
            Log::debug($msg);
            return null;
        }
    }
    public function getCompletedOrder($start_date, $end_date) {
        try {
            $orders = $this->getOrderDataByPeriod($start_date, $end_date);
            // Classify each payment channels
            $dataSet = array();
            foreach ($orders as $order) {
                $name = $this->paymentChannels[$order['payment_method']];
                if (!isset($dataSet[$name])) {
                    $dataSet[$name] = 1;
                } else {
                    $dataSet[$name] = $dataSet[$name] + 1;
                }
            }
            //s($dataSet);
            return $dataSet;
        }
        catch(Exception $ex) {
            $msg = sprintf('Exception = %s', $ex->getMessage());
            Log::debug($msg);
            return null;
        }
    }
    public function getOrderDataByPeriod($start_date, $end_date) {

        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $start_date)) {
            $message = 'Invalid format of start_date.';
            throw new Exception($message, 400);
        }
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $end_date)) {
            $message = 'Invalid format of end_date.';
            throw new Exception($message, 400);
        }

        $client = new GuzzleHttp\Client();
        $response = $client->post(Config::get('config.' . App::environment() . '.pcms_api_url') , array(
            'body' => array(
                'start_date' => $start_date,
                'end_date' => $end_date
            )
        ));
        //$json_result = $response->getBody()->getContents();
        $json_result = '{"status":"success","code":200,"message":"Successfully get order by period 2014-02-08 00:00:00 to 2014-02-08 23:59:59","data":[{"id":12150,"payment_method":"1","order_status":"new","payment_status":"failed","total_price":24000},{"id":12151,"payment_method":"8","order_status":"new","payment_status":"waiting","total_price":3900},{"id":12153,"payment_method":"8","order_status":"new","payment_status":"waiting","total_price":3900},{"id":12154,"payment_method":"1","order_status":"expired","payment_status":"expired","total_price":20000},{"id":12155,"payment_method":"8","order_status":"new","payment_status":"waiting","total_price":3900},{"id":12157,"payment_method":"1","order_status":"expired","payment_status":"expired","total_price":8900},{"id":12158,"payment_method":"1","order_status":"new","payment_status":"reconcile","total_price":3700},{"id":12160,"payment_method":"1","order_status":"expired","payment_status":"expired","total_price":8900},{"id":12161,"payment_method":"1","order_status":"expired","payment_status":"expired","total_price":8900},{"id":12163,"payment_method":"1","order_status":"new","payment_status":"reconcile","total_price":8900},{"id":12169,"payment_method":"1","order_status":"new","payment_status":"reconcile","total_price":17000},{"id":12174,"payment_method":"1","order_status":"expired","payment_status":"expired","total_price":9000},{"id":12176,"payment_method":"8","order_status":"new","payment_status":"reconcile","total_price":1700},{"id":12177,"payment_method":"5","order_status":"new","payment_status":"reconcile","total_price":22000},{"id":12180,"payment_method":"1","order_status":"expired","payment_status":"expired","total_price":80000},{"id":12182,"payment_method":"1","order_status":"new","payment_status":"reconcile","total_price":20000},{"id":12184,"payment_method":"5","order_status":"new","payment_status":"waiting","total_price":240},{"id":12185,"payment_method":"8","order_status":"new","payment_status":"reconcile","total_price":440},{"id":12189,"payment_method":"1","order_status":"new","payment_status":"reconcile","total_price":420}],"count":19}';
        $output = json_decode($json_result, true);
        //sd($output);
        $orders = $output['data'];
        //sd($orders);
        return $orders;
    }
}
