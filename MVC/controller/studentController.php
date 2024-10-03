<?php
    class StudentController
    {
        public $Obj;
        function __construct()
        {
            if(file_exists("./model/studentModel.php"))
            {
                include_once "./model/studentModel.php";
            }
            $this->Obj=new StudentModel();
        }

        //This function is for studentsList
        function studentList()
        {   
            if(isset($_SESSION["Name"]))
            {
                $arr=array();
                $arr['department']=isset($_POST['dept_filter']) ? $_POST['dept_filter'] : null;
                $arr['blood_group']=isset($_POST['blood_group_filter']) ? $_POST['blood_group_filter'] : null;
                $arr['passedout_year']=isset($_POST['passedout_year_filter']) ? $_POST['passedout_year_filter'] : null;
                //array_filter - remove null values
                $filteredArray=array_filter($arr);
                $rows=$this->Obj->studentList($filteredArray);
                $results_per_page=5;
                $number_of_page = ceil ($rows/$results_per_page);
                if(!isset($_GET['pageId']))
                {  
                    $page=1;  
                }
                else
                {  
                    $page=$_GET['pageId'];  
                }
                $page_first_result = ($page-1) * $results_per_page;
                $finalResult=$this->Obj->retrieve($filteredArray,$page_first_result,$results_per_page);
                if(file_exists("./view/studentList.php"))
                {   
                    include_once "./view/studentList.php";
                }
            }
            else
            {
                $this->redirect_login();
            }
        }

        //This function is for studentData Form
        function studentForm()
        {
            if(isset($_SESSION["Name"]))
            {
                if(isset($_POST['submit']))
                {
                    $arr=array();
                    $arr['firstname']=$_POST['firstname'];
                    $arr['lastname']=$_POST['lastname'];
                    $arr['dob']=$_POST['dob'];
                    $arr['email']=$_POST['email'];
                    $arr['number']=$_POST['number'];
                    $imagePath=$this->file_upload();
                    $arr['image']=$imagePath;
                    $arr['department']=$_POST['department'];
                    $arr['age']=$_POST['age'];
                    $arr['blood_group']=$_POST['blood_group'];
                    $arr['gender']=$_POST['gender'];
                    $arr['passedout_year']=$_POST['passedout_year'];
                    $arr['location']=$_POST['location'];
                    $this->Obj->studentForm($arr);
                    header("Location: index.php?mod=student&view=studentList");
                }
                if(file_exists("./view/studentForm.php"))
                {
                    include_once "./view/studentForm.php";
                }
            }
            else
            {
                $this->redirect_login();
            }
            
        }

        //This function is for get particular student details
        function getStudentDetails()
        {
            if(isset($_SESSION["Name"]))
            {
                $id=$_GET['id'];
                $rows=$this->Obj->getStudentDetails($id);
                if(file_exists("./view/updateForm.php"))
                {
                    include_once "./view/updateForm.php";
                }
            }
            else
            {
                $this->redirect_login();
            }
        }

        //This function is for updateData Form
        function updateForm()
        {
            if(isset($_SESSION["Name"]))
            {
                $id=$_GET['id'];
                if(isset($_POST['submit']))
                {
                    $arr=array();
                    $arr['id']=$id;
                    $arr['firstname']=$_POST['firstname'];
                    $arr['lastname']=$_POST['lastname'];
                    $arr['dob']=$_POST['dob'];
                    $arr['email']=$_POST['email'];
                    $arr['number']=$_POST['number'];
                    $imagePath=$this->file_upload();
                    $arr['image']=$imagePath;
                    $arr['department']=$_POST['department'];
                    $arr['age']=$_POST['age'];
                    $arr['blood_group']=$_POST['blood_group'];
                    $arr['gender']=$_POST['gender'];
                    $arr['passedout_year']=$_POST['passedout_year'];
                    $arr['location']=$_POST['location'];
                    $rows=$this->Obj->updateForm($arr);
                    $this->studentList();
                }
            }
            else
            {
                $this->redirect_login();
            }
        }

        //This function is for delete student details (Note: Here Soft Delete)
        function deleteStudent()
        {
            if(isset($_SESSION["Name"]))
            {
                $id=$_GET['id'];
                $this->Obj->deleteStudent($id);
                $this->studentList();
            }
            else
            {
                $this->redirect_login();
            }
        }

        //This function is for view full details about particular student
        function viewStudent()
        {
            if(isset($_SESSION["Name"]))
            {
                $id=$_GET['id'];
                $data=$this->Obj->getStudentDetails($id);
                if(file_exists("./view/studentView.php"))
                {
                    include_once "./view/studentView.php";
                }
            }
            else
            {
                $this->redirect_login();
            }
        }

        //This function is for login purpose
        function login()
        {
            if(isset($_SESSION["Name"]))
            {
                header("Location: index.php?mod=student&view=studentList");
            }
            else
            {
                if(isset($_POST['username']) && isset($_POST['password']))
                {
                    $username=$_POST['username'];
                    $givenpassword=$_POST['password'];
                    $dbPassword=$this->Obj->login($username);
                    $row=$dbPassword->fetch(PDO::FETCH_ASSOC);
                    if($givenpassword==$row['password'])
                    {
                        $_SESSION["Name"]=$username;
                        header("Location:index.php?mod=student&view=studentList");
                    }
                    else
                    {
                        header("Location:index.php");
                    }
                } 
                else
                {
                    $this->redirect_login();
                }
            }
        }

        //This function is for show the login page if the user not logged in
        function redirect_login()
        {
            if(file_exists("view/login.php"))
            {
                include_once "view/login.php";
            }
        }

        //This function is for image upload purpose
        function file_upload()
        {
            if(isset($_SESSION["Name"]))
            {
                    if ($_FILES["image"]["error"]==0) 
                    {   
                        $FileTypes = ['jpg', 'jpeg', 'png'];
                        $uploadDirectory = 'C:/xampp/htdocs/MVC/view/Images';
                        $tmpName = $_FILES["image"]["tmp_name"];
                        $name = $_FILES["image"]["name"];
                        //pathinfo - give information about path
                        //PATHINFO_EXTENSION - take only extension
                        //strtolower - convert to lower case
                        $fileExtension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        //in_array - check the value present in the array or not
                        if(in_array($fileExtension, $FileTypes)) 
                        {
                            //move_uploaded_file - move the uploaded file from temp to local folder
                            move_uploaded_file($tmpName, "$uploadDirectory/$name");
                            $imagePath = "/MVC/view/Images"."/$name";
                            return $imagePath;
                        }
                    }
                    else
                    {
                        $id=$_GET["id"];
                        $oldImagePath=$this->Obj->getOldImage($id);
                        $imagePath=$oldImagePath["image"];
                        return $imagePath;
                    }
            }
        }

        //This function is for logout purpose
        function logout()
        {
            //session_destroy - delete all data's that are stored in session
            session_destroy();
            header("Location: index.php?mod=student&view=login");
        }

        //This magic method will execute if user call undefined view
        function __call($name, $arguments)
        {
            isset($_SESSION["Name"]) ? header("Location: index.php?mod=student&view=studentList") : header("Location: index.php?mod=student&view=login");
        }    
    }