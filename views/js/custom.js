$(document).ready(function(){
    
    var docCookies = {
        getItem: function (sKey) {
            if (!sKey || !this.hasItem(sKey)) {
                return null;
            }
            return unescape(document.cookie.replace(new RegExp("(?:^|.*;\\s*)" + escape(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*((?:[^;](?!;))*[^;]?).*"), "$1"));
        },
        /**
		* docCookies.setItem(sKey, sValue, vEnd, sPath, sDomain, bSecure)
		*
		* @argument sKey (String): the name of the cookie;
		* @argument sValue (String): the value of the cookie;
		* @optional argument vEnd (Number, String, Date Object or null): the max-age in seconds (e.g., 31536e3 for a year) or the
		*  expires date in GMTString format or in Date Object format; if not specified it will expire at the end of session; 
		* @optional argument sPath (String or null): e.g., "/", "/mydir"; if not specified, defaults to the current path of the current document location;
		* @optional argument sDomain (String or null): e.g., "example.com", ".example.com" (includes all subdomains) or "subdomain.example.com"; if not
		* specified, defaults to the host portion of the current document location;
		* @optional argument bSecure (Boolean or null): cookie will be transmitted only over secure protocol as https;
		* @return undefined;
		**/
        setItem: function (sKey, sValue, vEnd, sPath, sDomain, bSecure) {
            if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/.test(sKey)) {
                return;
            }
            var sExpires = "";
            if (vEnd) {
                switch (typeof vEnd) {
                    case "number":
                        sExpires = "; max-age=" + vEnd;
                        break;
                    case "string":
                        sExpires = "; expires=" + vEnd;
                        break;
                    case "object":
                        if (vEnd.hasOwnProperty("toGMTString")) {
                            sExpires = "; expires=" + vEnd.toGMTString();
                        }
                        break;
                }
            }
            document.cookie = escape(sKey) + "=" + escape(sValue) + sExpires + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "") + (bSecure ? "; secure" : "");
        },
        removeItem: function (sKey) {
            if (!sKey || !this.hasItem(sKey)) {
                return;
            }
            var oExpDate = new Date();
            oExpDate.setDate(oExpDate.getDate() - 1);
            document.cookie = escape(sKey) + "=; expires=" + oExpDate.toGMTString() + "; path=/";
        },
        hasItem: function (sKey) {
            return (new RegExp("(?:^|;\\s*)" + escape(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie);
        }
    };//End Of Doc Cookies



// Changing the repoted link text 

    $('.report a').each(function(){
    
        var link_id = $(this).attr('id');
    
        if(docCookies.hasItem('vs_data'))
            var prev_data = docCookies.getItem('vs_data');
        else 
            var prev_data='';
        
   
         
        var ar= Array();
        var ar= prev_data.split('-');
        
         
//        for(var x in ar){
//          
//            if( ar[Number(x)]== link_id){
//                $(this).text('Reported');
//                break;
//                 
//            }
//             
//             
//        }  
    
    
    })
        
        
    
    //autocomplete
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
    
//    $("#tut_submit").click(function(e){
//        e.preventDefault();
//        
//        var courseId = $('#select_course').val();
//        var courseName = $('#select_course option:selected').text();
//        var batchId = $('#course_batch').val();
//        //Getting content using ajax
//        $.ajax({
//            
//            type :  "post",
//            url : ajaxUrl,
//            timeout : 5000,           
//            data : {
//                'action' : 'get_table_data',
//                'course_id' : courseId,
//                'course_name': courseName,
//                'batch_id': batchId
//            },
//            success :  function(data){
//                alert(data);
//            }
//            
//        } )
//                
//    })
    
    //Link reporting as Dead
    $('.report a').click(function(e){
        var this_link = $(this);
        e.preventDefault();
        var link_id_main=$(this).attr('id');
        
        var rew=/[a-z]+/;
        var red=/\d+/;
        
      var action =  rew.exec(link_id_main);
       var link_id = red.exec(link_id_main);
       action = action[0];
       link_id = link_id[0];
       

        
       
        if(docCookies.hasItem('vs_data'))
            var prev_data = docCookies.getItem('vs_data');
        else 
            var prev_data='';
        
   
         
        var ar= Array();
        var ar= prev_data.split('-');
        
         
        for(var x in ar){
          
            if( ar[Number(x)]== link_id){
                alert('You have already reported this link');
                return;
                 
            }
             
             
        }
             
         
         
       
         
        var report_ajax_url = ajaxUrl+'&m=report_link';
        
         
         
        //report using ajax
         
        $.ajax({
            
            type :  "get",
            url : report_ajax_url,
            timeout : 5000,           
            data : {
                'action' : 'report_data',
                'link_id' : link_id,
                'todo': action
                
              
            },
            success :  function(data){
               
                if( prev_data == '' )
                    docCookies.setItem( 'vs_data', link_id, null, '/');
                else                   
                    docCookies.setItem( 'vs_data', prev_data+'-'+link_id, null, '/');
                
                var vote_count =  this_link.next().text();
                
               var vc =Number (red.exec(vote_count));
                
                 this_link.next().text('('+ Number(vc+1)+')');
                
                
               
               
            }
            
        } )
              
         
  
         
        
        
    })
    
})