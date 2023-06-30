<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
    
 //レスポンス用のデータの枠組みを用意
    $response=[
        "result" => "error", //実行結果を格納(success or error) 成功時にsuccessに書き換える
        "errCode" => null,  //エラーコードがある場合、格納する
        "errMsg" => null,   //エラーメッセージがある場合、格納する
        "userId" => "",
        "userName"=>"",
        "profile"=>"",
        "userFollowFlg"=>False,
        "followCount"=>"",
        "followerCount"=>"",
        "whisperList" => [],
        "goodList" =>[],
    ];
    
    //リクエストの解析
    if($_SERVER['REQUEST_METHOD'] === 'POST'){   //POSTか確認
        /**
	* POSTで送られてきていた場合、JSON形式のデータを取得し、配列に加工。
	* 1. file_get_contents('php://input')：HTTPリクエストのボディ部分からデータを取得(php://inputストリームを使用)
	* 2. json_decode：取得したJSON形式のデータを配列に変換
	* 3. 変数($postData)に変換した配列のデータを格納
	**/
        
        $postData = json_decode(file_get_contents('php://input'),true);
       
    }else{
        //パラメータがない場合エラーメッセージ(ユーザーID:006,パスワード:007)
        
        echo "error";
        exit();
    }
    require_once './common/errorMsgs.php';//エラーメッセージファイル読み込み
    
    
    //パラメータチェック
    if($postData["userId"] != ""){
        //内容があれば
        $userId = $postData["userId"];  //取得したメールアドレス代入
    }else{
        //ない場合
       errResult('006');
       exit();
    }
    
    if($postData["LoginUserId"]!= ""){
        //内容があれば
        $LoginUserId = $postData["LoginUserId"];  //取得したパスワード代入
    }else{
        //ない場合
       errResult('015');
       exit();
    }
    
    
     require_once "./common/mysqlConnect.php"; //database接続
        
    
    try{
        
            // 送られてきたユーザIDとパスワードと一致するデータを取得する     
            $sql = "select u.userId, u.userName, u.profile, w.cnt as whisperCnt, fl.cnt as followCnt, fr.cnt as followerCnt ";
            $sql .= "from user u left join whisperCntView w on w.userId=u.userId left join followCntView fl on fl.userId=w.userId left join followerCntView fr on fr.followUserId=w.userId ";
            $sql .= "WHERE u.userId = :userId";

            $stmt = $pdo->prepare($sql);   
            $stmt -> bindParam(":userId",$userId,PDO::PARAM_STR);
            $stmt -> execute();

            while($row = $stmt->fetch()){
                $response["userId"] = $row["userId"];
                $response["userName"] = $row["userName"];
                $response["profile"] = $row["profile"];
                if($row["followCnt"]==null){
                    $response["followCount"] = 0;
                }else{
                    $response["followCount"] = $row["followCnt"];
                }
                if($row["followerCnt"]==null){
                    $response["followerCount"] = 0;
                }else{
                    $response["followerCount"] = $row["followerCnt"];
                }
                if($row==null){
                    errResult('004');
                    exit();
                }
            }
            $stmt = null;
            // 送られてきたユーザIDとパスワードと一致するデータを取得する     
            $sql = "select count(followUserId) as cnt from follow where userId = :userId and followUserId = :LoginUserId";

            $stmt = $pdo->prepare($sql);   
            $stmt -> bindParam(":userId",$LoginUserId,PDO::PARAM_STR);
            $stmt -> bindParam(":LoginUserId",$userId,PDO::PARAM_STR);
            $stmt -> execute();

            while($row = $stmt->fetch()){
                if($row["cnt"]==1){
                    $response["userFollowFlg"] = TRUE;
                }
            }
            $stmt=null;
            
            $sql1 = "SELECT w.whisperNo, w.userId, u.userName, w.postDate, w.content ";
            $sql1 .= "FROM whisper w left join user u on u.userId = w.userId ";
            $sql1 .= "where w.userId = :userId order by w.postDate DESC";
            $stmt = $pdo->prepare($sql1);   
            $stmt -> bindParam(":userId",$userId,PDO::PARAM_STR);
            $stmt -> execute();

            while($row = $stmt->fetch()){
                if($row==null){
                    errResult('004');
                    exit();
                }
                // $flag = TRUE;
                // if($row["loginUser"]==null){
                //     $flag = FALSE;
                // }
                array_push($response["whisperList"],[
                    "whisperNo" => $row["whisperNo"],
                    "userId" => $row["userId"],
                    "userName" => $row["userName"],
                    "postDate" => $row["postDate"],
                    "content" => $row["content"],
                    "goodFlg" => FALSE
                ]);
            }
            $stmt=null;

            for ($i=0; $i<count($response["whisperList"]);$i++){
                $sql = "select userId, whisperNo from goodInfo where whisperNo = :whisperNo and userId = :LoginUserId";
                $stmt = $pdo->prepare($sql);
                $stmt -> bindParam(":whisperNo",$response["whisperList"][$i]["whisperNo"],PDO::PARAM_STR);
                $stmt -> bindParam(":LoginUserId",$LoginUserId,PDO::PARAM_STR);
                $stmt -> execute();
                while($row = $stmt->fetch()){
                    $response["whisperList"][$i]["goodFlg"] = TRUE;
                }
                $stmt=null;
            }
            
            $sql1 = "select w.whisperNo, w.userId, u.userName, w.postDate, w.content, g.whisperno, g.cnt ";
            $sql1 .= "from whisper w left join user u on u.userId=w.userId left join goodCntView g on g.whisperNo=w.whisperNo ";
            $sql1 .= "where w.userId = :userId order by w.postDate DESC";
            $stmt = $pdo->prepare($sql1);   
            $stmt -> bindParam(":userId",$userId,PDO::PARAM_STR);
            $stmt -> execute();

            while($row = $stmt->fetch()){
                if($row["cnt"]==null){
                    $num = 0;
                }else{
                    $num = $row["cnt"];
                }
                array_push($response["goodList"],[
                    "whisperNo" => $row["whisperNo"],
                    "userId" => $row["userId"],
                    "userName" => $row["userName"],
                    "postDate" => $row["postDate"],
                    "content" => $row["content"],
                    "goodCount" => $num
                ]);
            }
            $stmt=null;
            
            
        $response["result"] = "success";
    } catch (PDOException $e) {
           throw new PDOException($e->getMessage(),(int)$e->getCode());
    }    

    $stmt = null;   //SQL情報クローズ

    require_once './common/mysqlClose.php';    //データベース接続解除
    
    
    //レスポンスの送信
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

    
    
    
   
    