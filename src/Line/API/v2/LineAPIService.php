<?php

namespace Hoangtu92\LaravelLineLogin\Line\API\v2;

use Hoangtu92\LaravelLineLogin\Http\Client;
use Hoangtu92\LaravelLineLogin\Line\API\v2\Response\AccessToken;
use Hoangtu92\LaravelLineLogin\Line\API\v2\Response\Profile;
use Hoangtu92\LaravelLineLogin\Line\API\v2\Response\Verify;

use Illuminate\Support\Facades\Log;

class LineAPIService {

    public function accessToken($code){
      $lineAPI = new LineAPI;
      $json_result = json_decode(self::getClient($lineAPI->accessToken($code, env('LINE_CHANNEL_ID'), env('LINE_CHANNEL_SECRET'), env('LINE_CALLBACK_URL'))), true);
      return array(
        $json_result['scope'],
        $json_result['access_token'],
        $json_result['token_type'],
        $json_result['expires_in'],
        $json_result['refresh_token'],
        $json_result['id_token']
      );
    }

    public function refreshToken($refresh_token){
      $lineAPI = new LineAPI;
      $json_result = json_decode(self::getClient($lineAPI->refreshToken($refresh_token, env('LINE_CHANNEL_ID'), env('LINE_CHANNEL_SECRET'))), true);
      return array(
        $json_result['scope'],
        $json_result['access_token'],
        $json_result['token_type'],
        $json_result['expires_in'],
        $json_result['refresh_token']
      );
    }

    public function verify($accessToken){
      $lineAPI = new LineAPI;
      $json_result = json_decode(self::getClient($lineAPI->verify($accessToken)), true);

      return array(
        $json_result['scope'],
        $json_result['client_id'],
        $json_result['expires_in']);
      }

    public function revoke($refresh_token){
      $lineAPI = new LineAPI;
      $json_result = json_decode(self::getClient($lineAPI->revoke($refresh_token)), true);
      return "Ok";
    }

    public function profile($accessToken)
    {
        $lineAPI = new LineAPI;
        $json_result = json_decode(self::getClient($lineAPI->profile(self::addBearer($accessToken))), true);

        if (!array_key_exists('statusMessage', $json_result)) {
            $json_result['statusMessage'] = " ";
        }
        return [
            "name" => $json_result['displayName'],
            "id" => $json_result['userId'],
            "pictureUrl" => $json_result['pictureUrl'],
            "statusMessage" => $json_result['statusMessage']
        ];
    }

    public function verifyIdToken($id_token){
      $lineAPI = new LineAPI;
        $json_result = json_decode(self::getClient($lineAPI->verifyIdToken($id_token)), true);

        if (!array_key_exists('statusMessage', $json_result)) {
            $json_result['statusMessage'] = " ";
        }
        return [
            "name" => $json_result['name'],
            "pictureUrl" => $json_result['picture'],
            "email" => $json_result['email']
        ];
      
    }

    public static function getLineWebLoginUrl($state) {
        $encodedCallbackUrl = urlencode(env('LINE_CALLBACK_URL'));
        //return "https://access.line.me/dialog/oauth/weblogin?response_type=code" . "&client_id=" . env('LINE_CHANNEL_ID') . "&redirect_uri=" . $encodedCallbackUrl . "&state=" . $state;
        return "https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=" . env('LINE_CHANNEL_ID') . "&redirect_uri=" . $encodedCallbackUrl . "&state=". $state ."&scope=profile%20openid%20email";
    }

    private function getClient($data){
      Log::info("LineAPIService.getClient: " . json_encode($data));
      $client = new Client;
      if ($data['Method'] == 'post'){
        $output = $client->httpPost($data['Url'], $data['Header'], $data['Body']);
      } elseif ($data['Method'] == 'get') {
        $output = $client->httpGet($data['Url'], $data['Header']);
      }
      Log::info("LineAPIService.getClient: " . $output);
      return $output;
    }



    private function addBearer($accessToken){
      return "Bearer " . $accessToken;
    }
}
