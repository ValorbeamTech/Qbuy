<?php

// controller class, contains all logics of an application

class Controller
{
    private String $token     = '';
    private array $data = array();
    protected PDO $db_connection;
    public function __construct(PDO $connection)
    {
        getHttpRequestHeaders();
        $this->db_connection = $connection;
    }
    // authenticate user
    public function auth_user()
    {
        $post_data          = file_get_contents('php://input');
        $post_data_clean    = json_decode(omit_newlines($post_data), true);
        $result             = $this->generate_auth_token($post_data_clean);
        echo json_encode($result);
    }

    // generate auth token with every request
    protected function generate_auth_token($data_params = array())
    {
        $model                       = new Model('hdb_users');
        $headers                     = apache_request_headers();
        if (key_exists('authorization', $headers)) {
            $this->token = substr($headers['authorization'],7);
        }
       
        if($this->token){
            $user_data                 = $model->read(['auth_token'=>$this->token]);  
            if($user_data['message']){
                if($user_data['message']['username'] !== $data_params['username']){
                    $this->data['success'] = false;        
                    $this->data['message'] = 'username is not correct!';
                    echo json_encode($this->data);
                    $this->setLoginAttempt($user_data['message']['id']);
                    die;
                } 
               
                if(verify_password($user_data['message']['password'], $data_params['password'])){
                    $this->data['success'] = false;            
                    $this->data['message'] = 'password is not correct!';
                    echo json_encode($this->data);
                    $this->setLoginAttempt($user_data['message']['id']);
                    die;
                }     
            }else{
                    $this->data['success'] = false;            
                    $this->data['message'] = 'user not found!';
                    echo json_encode($this->data);
                    die;
            }          
            
           
        }else{
            $this->noTokenResponse();
        }
        
        $new_token                          = generate_random_string(100);
        $modified_user_data                 = [];
        $modified_user_data['key_name']     = 'auth_token';
        $modified_user_data['key_value']    = $user_data['message']['auth_token'];
        $modified_user_data['auth_token']   = $new_token;
        $result                             = $model->change($modified_user_data);
        if($result){
            $sql     = "SELECT * FROM hdb_users WHERE auth_token = ? AND deleted = ?";
            $getData = $model->query($sql, [$new_token,0]);
            if($getData){
                $this->resetLoginAttempt($getData['message']['id']);
                $this->data['success'] = true;
                $this->data['message'] = $getData['message'];
                return $this->data;
            }
        }else{
            $this->noTokenResponse();
        }
           
    }

    // response when no token found
    protected function noTokenResponse(): string {
        $this->data['success'] = false;
        $this->data['message'] = 'attach auth token in request';
        echo json_encode($this->data);
        die;
    }

    // set login attempts
    protected function setLoginAttempt(int $id){
        $model                 = new Model('hdb_users', $id);
        $result                = $model->getResult()['message'];
        $trials                = $result['login_attempts'] + 1;
        $payload               = array(
            "key_name"         => "id",
            "key_value"        => $id,
            "login_attempts"   => $trials
        );
        $model->change($payload);
        
    }
    // reset login attempts
    protected function resetLoginAttempt(int $id){
        global $current_time;
        $model                 = new Model('hdb_users', $id);
        $trials                = 0;
        $payload               = array(
            "key_name"         => "id",
            "key_value"        => $id,
            "login_attempts"   => $trials,
            "last_login"       => $current_time
        );
        $model->change($payload);
        
    }
    // logout user 
    public function logout(int $id){
        global $current_time;
        $model                 = new Model('hdb_users', $id);
        $payload               = array(
            "key_name"         => "id",
            "key_value"        => $id,
            "last_logout"       => $current_time
        );
        return json_encode($model->change($payload));
    }
}
