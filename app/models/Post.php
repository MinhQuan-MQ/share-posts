<?php

class Post {

    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getPosts(){
        $query = 'SELECT *, posts.id as postId, users.id as userId FROM posts ';
        $query .= ' INNER JOIN users ON posts.user_id = users.id ';
        $query .= ' ORDER BY posts.created_at DESC ';
        $this->db->query($query);

        $results = $this->db->resultSet();

        return $results;
    }

    public function addPost($data){
        $query = 'INSERT INTO posts(title, user_id, body) VALUES( :title, :user_id, :body ) ';
        $this->db->query($query);
        // Bind values
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':body', $data['body']);

        // Execute
        if($this->db->execute()){
            return true;
        }else{
            return false;
        }
    }

    public function updatePost($data){
        $query = ' UPDATE posts SET title = :title, body = :body WHERE id = :id ';
        $this->db->query($query);
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':body', $data['body']);

        // Execute
        if($this->db->execute()){
            return true;
        }else{
            return false;
        }
    }

    public function getPostById($id){
        $query = ' SELECT * FROM posts WHERE id = :id ';
        $this->db->query($query);
        // Bind values
        $this->db->bind(':id', $id);

        $row = $this->db->single();
        return $row;
    }

    public function deletePost($id){
        $query = ' DELETE FROM posts WHERE id = :id ';
        $this->db->query($query);
        // Bind values
        $this->db->bind(':id', $id);

        // Execute
        if($this->db->execute()){
            return true;
        }else{
            return false;
        }
    }

}

?>