<?php

error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{

    $inputJSON = file_get_contents('php://input');
    if ($inputJSON === false)
    {
        http_response_code(400);
        exit();
    }
    $input = json_decode($inputJSON, TRUE);
    if ($input === null)
    {
        http_response_code(400);
        exit();
    }
    if (!array_key_exists("searchQuery", $input)){
        http_response_code(400);
        exit();
    }

    $db = mysqli_connect('localhost', 'root', '', 'test_assignment');

    if(!$db)
    {
        http_response_code(500);
        exit();
    }
    
    function searchComments($db, $query)
    {
        $resultArray = array();
        try
        {
            if($stmt = $db->prepare('SELECT `post`.`title`, `comment`.`body` FROM `comment` LEFT JOIN `post` on (`comment`.`postId` = `post`.`id`) WHERE `comment`.`body` like ?'))
            {
                $parameter = "%{$query}%";
                $stmt->bind_param('s', $parameter);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $resultArray[] = array(
                        "postTitle" => $row['title'],
                        "commentBody" => $row['body'],
                    );
                }
            }       
        }
        catch (Exception $e)
        {
            //print($e);
        }
        return $resultArray;
    }

    $searchResults = searchComments($db, $input["searchQuery"]);

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($searchResults);
    exit();
}
elseif($_SERVER['REQUEST_METHOD'] === 'GET')
{
    ?>
    <!doctype html>
    <html>
        <head>
            <link href="assets/style.css" rel="stylesheet">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
            <script src="assets/script.js"></script>
        </head>
        <body> 
            <main>
                <section>
                    <h1>Поиск</h1>
                    <form id="searchForm" class="needs-validation" method="post">
                        <div class="input-group">
                            <label for="query" class="form-label">Строка для поиска</label>
                            <input type="text" class="form-control" id="query" name="query" placeholder="Поиск" autocomplete="off"/>
                            <div class="invalid-feedback"></div>
                        </div>
                        <button type="submit" class="btn-calc">Найти</button>
                    </form>
                    <hr>
                    <div id="searchResults">
                    </div>
                </section>
            </main>
        </body>
    </html>
    <?php
    exit();
}

