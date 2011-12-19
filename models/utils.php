<?php

class Utils extends CI_Model{
    
    function __construct() {
        parent::__construct();
    }
    
    function insert_movie($data){
        extract($data);
        $names= array('movie_name', 'movie_channel_link', 'movie_direct_links', 'movie_release_date','movie_release_countries');
        foreach($names as $val){
        $this->$val= $$val;
        }
       return $this->db->insert('vs_movies',$this);
       
    }
    
    //fetch a single course 
    function get_course($id){
        return $this->db->query("select * from t_course where iCourseId =$id");
        
    }
    
    function get_teacher($id){
        return $this->db->query("select * from t_teacher where iteacherid=$id");
    }
    
    function get_all($table){
        return $this->db->get($table)->result();        
    }
    public function insert(){                
        $this->ICourseId = $this->input->post('id');
        $this->vCourseName = $this->input->post('name');
        $this->dubCourseCredit = $this->input->post('credit');
        $this->ICourseType = $this->input->post('type');
        $this->dubFullMarks = $this->input->post('fullmarks');
        $this->dDateOfLastUpdate = date('Y:m:d');
        return $this->db->insert('t_course', $this);
    }
    
        public function insert_students(){                
        $this->iStudentId = $this->input->post('id');
        $this->vStudentFirstName = $this->input->post('name');
        $this->vSessionNo = $this->input->post('session');
        $this->iClassRollNo = $this->input->post('class_roll');
        $this->dDateOfLastUpdate = date('Y:m:d');
        return $this->db->insert('t_student', $this);
    }
    
    public function delete_student($id){
        return $this->db->delete('t_student', array('IStudentId' =>$id));
    }
    public function delete_course($id){
        return $this->db->delete('t_course', array('ICourseId' =>$id));
    }
    
    
    public function update_teacher(){  
        $this->vTeacherFirstName = $this->input->post('vTeacherFirstName');
        $this->vTeacherMiddleName= $this->input->post('vTeacherMiddleName');
        $this->vTeacherLastName = $this->input->post('vTeacherLastName');
        $this->dDateOfJoining = $this->input->post('dDateOfJoining');
        $this->dDateOfBirth= $this->input->post('dDateOfBirth');
        $this->vTeacherDesignation = $this->input->post('vTeacherDesignation');
        $this->vCurrentAddress = $this->input->post('vCurrentAddress');
        $this->vPermanentAddress = $this->input->post('vPermanentAddress ');
        $this->vEmailAddress = $this->input->post('vEmailAddress');
        $this->vTelephoneNumber = $this->input->post('vTelephoneNumber');
        
        return $this->db->update('t_teacher', $this, array('iTeacherId'=>$this->input->post('iTeacherId')));
    }
    public function update_course(){
                $this->ICourseId = $this->input->post('id');
        $this->vCourseName = $this->input->post('name');
        $this->dubCourseCredit = $this->input->post('credit');
        $this->ICourseType = $this->input->post('type');
        $this->dubFullMarks = $this->input->post('fullmarks');
        $this->dDateOfLastUpdate = date('Y:m:d');
        return $this->db->update('t_course', $this,array('ICourseId' => $this->input->post('id')));
    }
    
    
}
?>
