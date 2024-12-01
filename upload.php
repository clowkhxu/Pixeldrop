<?php
include('WsUploadPicture.php');
$uploader = new \App\Upload\WsUpload();
if (isset($_POST['action']) && $_POST['action'] === 'upload-img') {
    if (isset($_FILES['file']) && count($_FILES['file']['tmp_name']) > 0) {
        $t = $_POST['ft'];
        $data = [];
        if (!in_array($t, ['imgur', 'sohu', 'imgbb', 'telegra'])) {
            die(json_encode(["status" => "error", "msg" => 'Not handled']));
        }
        foreach ($_FILES['file']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['file']['error'][$key] === UPLOAD_ERR_OK) {
                $pic = $_FILES['file']['tmp_name'][$key];
                $fileSize = $_FILES['file']['size'][$key];
                $fileType = mime_content_type($pic);
                if ($fileSize > 5 * 1024 * 1024) {
                    die(json_encode(["status" => "error", "msg" => "Maximum allowed size of 5MB."]));
                }
                if (!in_array($fileType, ['image/jpeg','image/jpg','image/png', 'image/gif'])) {
                    die(json_encode(["status" => "error", "msg" => "Invalid image type."]));
                }
                try {
                    $res = $uploader->$t($pic);
                    $data[] = [
                        'name' => $_FILES['file']['name'][$key],
                        'size' => round($fileSize / 1024 / 1024, 2) . ' MB',
                        'link' => $res
                    ];
                } catch (\Exception $e) {
                    die(json_encode(["status" => "error", "msg" => $e->getMessage()])); 
                }
            }
        }
        if (!empty($data)) {
            die(json_encode(["status" => "success", "data" => $data, "msg" => 'Uploaded successfully!']));
        } else {
            die(json_encode(["status" => "error", "msg" => "No valid files uploaded!"]));
        }
    } else {
        die(json_encode([
            "status" => "error",
            "msg" => "No file uploaded!"
        ]));
    }
} else {
    die('<pre>400 / Bad Request</pre>');
}
