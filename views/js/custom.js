$(document).ready(function(){
    
     var options = { 
        serviceUrl : ajaxUrl,
        width:300
    };			
    $('#search_box').autocomplete(options);
    
    $("#select_course").change(function(){
        var courseId = $(this).val();
        var courseName = $('select option:selected').text();
    
        $("#course_id").attr("value", courseId);
            
           
    
    })
    
    //subimit form action
    
    $("#tut_submit").click(function(e){
        e.preventDefault();
        
          var courseId = $('#select_course').val();
        var courseName = $('#select_course option:selected').text();
        var batchId = $('#course_batch').val();
        //Getting content using ajax
        $.ajax({
            
            type :  "post",
            url : ajaxUrl,
            timeout : 5000,           
            data : {
                'action' : 'get_table_data',
                'course_id' : courseId,
                'course_name': courseName,
                'batch_id': batchId
            },
            success :  function(data){
                alert(data);
            }
            
        } )
                
    })
    
    //Link reporting as Dead
    $('.report a').click(function(e){
         e.preventDefault();
         var link_id=$(this).attr('id');
       
         
         var report_ajax_url = ajaxUrl+'&m=report_link';
        
         
         
         //report using ajax
         
                 $.ajax({
            
            type :  "get",
            url : report_ajax_url,
            timeout : 5000,           
            data : {
                'action' : 'report_data',
                'link_id' : link_id
              
            },
            success :  function(data){
                alert(data);
            }
            
        } )
              
         
  
         
        
        
    })
    
})