<?php

error_reporting(0);

$db = mysqli_connect('localhost', 'root', '', 'test_assignment');

if(!$db)
{
    die('Ошибка подключения к БД');
}

function savePostToDB($db, $post)
{
    try
    {
        if($stmt = $db->prepare('INSERT INTO post (id, userId, title, body) VALUES (?, ?, ?, ?)'))
        {
            $stmt->bind_param('iiss', $post->id, $post->userId, $post->title, $post->body);
            $stmt->execute();
        }
        else
        {
            return false;
        }
        
    }
    catch (Exception $e)
    {
        return false;
    }
    return true;
}

function saveCommentToDB($db, $comment)
{
    try
    {
        if($stmt = $db->prepare('INSERT INTO comment (id, postId, name, email, body) VALUES (?, ?, ?, ?, ?)'))
        {
            $stmt->bind_param('iisss', $comment->id, $comment->postId, $comment->name, $comment->email, $comment->body);
            $stmt->execute();
        }
        else
        {
            return false;
        }
        
    }
    catch (Exception $e)
    {
        return false;
    }
    return true;
}



$jsonPosts = file_get_contents('https://jsonplaceholder.typicode.com/posts');
$posts = json_decode($jsonPosts);

$savedPostCount = 0;

foreach ($posts as &$post)
{
    if(savePostToDB($db, $post))
    {
        $savedPostCount++;
    }
    else
    {
        echo "Не удалось сохранить запись";
    }
}
unset($post);

$jsonComments = file_get_contents('https://jsonplaceholder.typicode.com/comments');
$comments = json_decode($jsonComments);

$savedCommentCount = 0;

foreach ($comments as &$comment)
{
    if(saveCommentToDB($db, $comment))
    {
        $savedCommentCount++;
    }
    else
    {
        echo "Не удалось сохранить комментарий";
    }
}
unset($comment);

echo "Загружено $savedPostCount записей и $savedCommentCount комментариев";