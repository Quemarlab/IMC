<?php
require '../config.php';
ob_start();
class UserVerification extends Database
{
    public function session()
    {
        try {

            if (isset($_COOKIE['remember'])) {
                $_SESSION['management'] = $_COOKIE['remember'];
            } elseif (!isset($_COOKIE['remember'])) {
                $expiry = 1800;
                if (isset($_SESSION['LAST']) && (time() - $_SESSION['LAST'] > $expiry)) {
                    session_destroy();
                    session_unset();
?>
                    <script>
                        window.alert('Your session has been expired, Login again');
                        self.location = '../index.php';
                    </script>
                <?php
                    $l_access = date('m-d-Y h:i:s');
                    $update = "UPDATE account_holders SET last_access = :last_access ";
                    $update = $this->con->prepare($update);
                    $data = [
                        ":last_access" => $l_access
                    ];
                    $assure = $update->execute($data);
                }
                $_SESSION['LAST'] = time();
            } else {
                ?>
                <script>
                    console.log('No session started, Please review login Process');
                </script>
                <?php


            }


            if (!isset($_SESSION['management'])) {
                header("location:../index");
            }
        } catch (PDOException $e) {
            echo "You can't login first solve this error" . $e->getMessage();
        }
    }

    public function getUser()
    {
        try {
            $email = $_SESSION['management'];
            $user = "SELECT * FROM account_holders WHERE email = :email";
            $user = $this->con->prepare($user);
            $user->execute([":email" => $email]);

            if ($user) {
                $user_value = $user->fetch(PDO::FETCH_ASSOC);
            } else {
                $user_value = [];
            }

            return $user_value;
        } catch (PDOException $e) {
            echo "The user based on session is not Active" . $e->getMessage();
        }
    }

    public function logout($account_id)
    {
        try {
            $query = "SELECT * FROM account_holders WHERE account_id =:account_id";
            $query = $this->con->prepare($query);
            $data = [
                ":account_id" => $account_id
            ];
            $query->execute($data);
            if ($query->rowCount() > 0) {
                session_unset();
                session_destroy();
                $last_access = date('m-d-Y h:i:s');
                $update = "UPDATE account_holders SET last_access = :last_access ";
                $update = $this->con->prepare($update);
                $data = [
                    ":last_access" => $last_access
                ];
                $assure = $update->execute($data);
                if ($assure) {
                    setcookie("remember", "" . $_SESSION['management'] . "", time() - 60 * 60 * 24 * 7);
                    setcookie("redirection", "management", time() - 60 * 60 * 24 * 7);
                    header("location:../index");
                } else {
                ?>
                    <script>
                        console.error('The redirection page is missing');
                    </script>
                <?php
                }
            } else {
                ?>
                <script>
                    console.error('That account does not exist');
                </script>
<?php
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getProjectLimit()
    {
        $query = "SELECT * FROM project LIMIT 3";
        $query = $this->con->prepare($query);
        $query->execute();

        if ($query->rowCount() > 0) {
            $data = $query->fetch(PDO::FETCH_ASSOC);    
        }
        else {
            $data = [];
        }

        return $data;
    }

    public function dashboardCount() {
        try {
            $queryP = $this->con->prepare("SELECT * FROM project");
            $queryP->execute();
            $project = $queryP->rowCount();

            $queryN = $this->con->prepare("SELECT * FROM news");
            $queryN->execute();
            $news = $queryN->rowCount();

            $queryG = $this->con->prepare("SELECT * FROM gallery");
            $queryG->execute();
            $gallery = $queryG->rowCount();

            $queryA = $this->con->prepare("SELECT * FROM account_holders");
            $queryA->execute();
            $users = $queryA->rowCount();

            $data = ['project' => $project, 'news' => $news, 'gallery' => $gallery, 'users' => $users];

            return $data;

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function userLocation($account_id) {
        try {
            $query = $this->con->prepare("SELECT * FROM access_logs WHERE account_id = ? ORDER BY id DESC LIMIT 1");

            $query->execute(array($account_id));
            
            if ($query->rowCount() > 0) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
            else {
                $data = [];
                throw new Exception("User not found", 1);
                
            }

            return $data;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        
    }

    public function getdashnews() {
        try {
            $query = $this->con->prepare("SELECT * FROM news ORDER BY id DESC LIMIT 5");
            $query->execute();

            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }
            }
            else {
                $data = [];
                throw new Exception("No news found", 1);
            }

            return $data;
        }
        catch (Exception $e) {
            $e->getMessage();
        }
    }

    public function getlastcontact() {
        try {
            $query = $this->con->prepare("SELECT * FROM contact ORDER BY id DESC LIMIT 1");
            $query->execute();
            if ($query->rowCount() > 0) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
            else {
                $data = [];
                throw new Exception("No contacts found", 1);
            }
            return $data;
        }
        catch (Exception $e) {
            $e->getMessage();
        }

    }
}

$session = new UserVerification();
$session->session();

?>