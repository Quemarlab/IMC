<?php
session_start();
class Database
{
    private $server = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "smc";
    public $userEmail;
    public $userPassword;
    protected $con;

    public function __construct()
    {
        try {
            $this->userEmail = "usabuwerakendlyquemar@gmail.com";
            $this->userPassword = "ajqevsxcgdtxqkra";
            $this->con = new PDO("mysql:host=$this->server;dbname=$this->dbname", $this->user, $this->pass);
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "The database connection refused to be established due to" . $e->getMessage();
        }
    }
}
