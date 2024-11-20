    <?php
require '../../config.php';

class Authentication extends Database
{

    public function UserLocation($account_id)
    {
        try {
            $ip_address = file_get_contents('https://ipinfo.io/ip');
            $ip_value = curl_init('http://ipwho.is/' . $ip_address);
            curl_setopt($ip_value, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ip_value, CURLOPT_HEADER, false);
            $json_value = curl_exec($ip_value);
            curl_close($ip_value);

            $result = json_decode($json_value, true);

            if ($result) {
                // We save the user location information
                $ip = $result['ip'];
                $country = $result['country'];
                $region = $result['region'];
                $city = $result['city'];
                $longitude = $result['longitude'];
                $latitude = $result['latitude'];

                $save_user_location = "INSERT INTO access_logs (account_id, ip_address, country, region, city, latitude, longitude) VALUES (? , ? , ? , ? , ? , ? , ?)";

                $save_user_location = $this->con->prepare($save_user_location);
                $save_user_location_data = [
                    $account_id,
                    $ip_address,
                    $country,
                    $region,
                    $city,
                    $latitude,
                    $longitude
                ];
                $save_user_location->execute($save_user_location_data);
            }
        } catch (PDOException $e) {
            echo "The user location is not able to be fetched" . $e;
        }
    }

    public function redirection($account_privilege, $email_address, $account_id)
    {
        try {
            $_SESSION[$account_privilege] = $email_address;
            if (isset($_POST['remember'])) {
                $remember = $_POST['remember'];
                $remember = filter_var($remember, FILTER_SANITIZE_STRING);
                setcookie("remember", "" . $_SESSION[$account_privilege] . "", time() + 60 * 60 * 24 * 7);
                setcookie("redirection", $account_privilege, time() + 60 * 60 * 24 * 7);
            }
            $last_access = "online";
            $online = "UPDATE account_holders SET last_access = :last_access ";
            $online = $this->con->prepare($online);
            $online->execute([":last_access" => $last_access]);
            setcookie("logintimes", "1", time() - 60 * 60);
            echo "success" . $account_privilege . "";
            $this->UserLocation($account_id);
        } catch (PDOException $e) {
            echo "Can't be redirected" . $e->getMessage();
        }
    }

    public function authentication($email, $password)
    {
        if (!empty($email) && !empty($password)) {
            try {
                $sql = "SELECT * FROM account_holders WHERE email=? OR username = ?";
                $sql = $this->con->prepare($sql);
                $sql->execute([$email, $email]);
                if ($sql->rowCount() > 0) {
                    $row = $sql->fetch(PDO::FETCH_ASSOC);
                    if ($row['status'] == "suspended") {
                        echo "The account is blocked based on login attempts exceed allowed,
                              Contact the management to unlock your account";
                    } else {
                        $password_check = password_verify($password, $row['password']);
                        if ($password_check) {
                            if ($row['status'] == "active") {
                                if ($row['privilege'] == "management") {
                                    $account_privilege = "management";
                                    $email_address = $row['email'];
                                    $account_id = $row['account_id'];
                                    $this->redirection($account_privilege, $email_address, $account_id);
                                } elseif ($row['privilege'] == "disabled") {
                                    echo "Your account have been suspended, Contact management for more.";
                                }
                            } else {
                                echo "The account is suspended, Contact developers-tech to unlock !";
                            }
                        } else {
                            echo "The password is incorrect.";
                            $login_access_attempt = $_COOKIE['logintimes'];
                            if (($login_access_attempt) <= 5) {
                                $current_times = $_COOKIE['logintimes'];
                                $addition_times = $current_times + 1;
                                setcookie("logintimes", "" . $addition_times . "", time() + 60 * 60 * 24 * 7);
                                if (($_COOKIE['logintimes']) >= 5) {
                                    $status = "suspended";
                                    $account_suspend = "UPDATE account_holders SET status = :status WHERE email = :email";
                                    $account_suspend = $this->con->prepare($account_suspend);
                                    $account_suspend->execute([":status" => $status, ":email" => $email]);
                                }
                            }
                        }
                    }
                } else {
                    echo $email . " - Email or Username doesn't exist, Try different.";
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        } else {
            echo "All Input field are required";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    setcookie('logintimes',1,time() + 60 * 60 * 24 * 7);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $authentication = new Authentication();
    $authentication->authentication($email, $password);
}
