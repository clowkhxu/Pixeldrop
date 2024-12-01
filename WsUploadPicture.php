<?php
namespace App\Upload;

/**
 * =========================================================
 * API WsUpload IMG PHP
 * Generated time: 05/09/2024
 * Author: https://t.me/wus_team
 * Note: Example of uploading an image using an external API
 * =========================================================
 */

class WsUpload
{
    private $telegra;
    private $sohu;
    private $imgbb;
    private $imgur;
    private $im_ge;
    public function __construct()
    {
        date_default_timezone_set("Asia/Ho_Chi_Minh"); // time
        $this->telegra = "https://telegra.ph/"; // use telegra
        $this->sohu = "https://changyan.sohu.com/api/2/comment/attachment"; // use sohu
        $this->imgbb = "https://zh-cn.imgbb.com/json"; // use imgbb
        $this->imgur = "https://api.imgur.com/3/image/"; // use imgur
        $this->im_ge = "https://im.ge/json"; // use im.ge
    }
    /**
     * Method Send
     *
     * @param string $url The API endpoint URL.
     * @param array $data The form data to send with the request.
     * @param array $headers Optional headers to set for the request.
     *
     * @return array The response data.
     *
     * @throws \Exception If the HTTP request fails.
     */
    private function upload($url, $data, $headers = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $res = curl_exec($ch);
        $http_error = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http_error !== 200) {
            throw new \Exception(
                "Error code: " . $http_error . " | Response: " . $res
            );
        }
        return json_decode($res, true);
    }
    /**
     * Method Telegra.ph.
     *
     * @param string $file
     *
     * @return string
     *
     * @throws \Exception
     */
    public function telegra($file)
    {
        $res = $this->upload($this->telegra . "upload", [
            "file" => curl_file_create($file),
        ]);
        if (isset($res[0]["src"])) {
            return $this->telegra . $res[0]["src"];
        }
        throw new \Exception("Failed upload from Telegra.ph");
    }

    /**
     * Method Sohu.
     *
     * @param string $file
     *
     * @return string
     *
     * @throws \Exception
     */
    public function sohu($file)
    {
        $res = $this->upload($this->sohu, ["file" => curl_file_create($file)]);
        $res = stripslashes(trim($res, '"'));
        $res = json_decode($res, true);
        if (isset($res["url"])) {
            return $res["url"];
        }
        throw new \Exception("Failed upload from Sohu");
    }

    /**
     * Method ImgBB.
     *
     * @param string $file
     *
     * @return string
     *
     * @throws \Exception
     */
    public function imgbb($file)
    {
        $res = $this->upload($this->imgbb, [
            "source" => curl_file_create($file),
            "type" => "file",
            "action" => "upload",
        ]);
        if (isset($res["image"]["url"])) {
          return $res["image"]["image"]["url"];
        }
        throw new \Exception("Failed upload from ImgBB");
    }

    /**
     * Method Imgur.
     *
     * @param string $file
     *
     * @return string
     *
     * @throws \Exception
     */
    public function imgur($file)
    {
        $client_id = "88fd52d307ecceb";
        $data = file_get_contents($file);
        $pvars = ["image" => base64_encode($data)];
        $headers = ["Authorization: Client-ID " . $client_id];
        $res = $this->upload($this->imgur, $pvars, $headers);
        if (isset($res["status"]) && $res["status"] === 200) {
            return $res["data"]["link"];
        }
        throw new \Exception("Failed upload from Imgur");
    }
    /**
     * Method im_ge.
     *
     * @param string $file
     *
     * @return string
     *
     * @throws \Exception
     */
    public function im_ge($file)
    {
        $url = $this->im_ge;
        $headers = ["Authorization: Bearer xxxxxxxxxxxxxxxx"];
        $res = $this->upload(
            $url,
            [
                "source" => curl_file_create($file),
                "type" => "file",
                "action" => "upload",
                "timestamp" => time(),
                "nsfw" => 0,
            ],
            $headers
        );
        if (isset($res["success"]) && $res["success"]["code"] === 200) {
            return $res["image"]["url"];
        }
        throw new \Exception("Failed upload from im_ge: " . json_encode($res));
    }
}