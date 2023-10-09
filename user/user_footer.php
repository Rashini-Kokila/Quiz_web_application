   <script type="text/javascript">
     var handle= setInterval(function(){
         var xmlhttp=new XMLHttpRequest();
         xmlhttp.open("GET","response.php",false);
         xmlhttp.send(null);
         var value = document.getElementById("time").innerHTML=xmlhttp.responseText;
         if(value=="Timeout" || value==''){
            clearInterval(handle);
            window.location.href = "time_out.php";
         }
         
      }, 1000);
   </script>
 <!-- jQuery-js include -->
 <script src="./reference/jquery-3.6.0.min.js.download"></script>
   <!-- Bootstrap-js include -->
   <script src="./reference/bootstrap.min.js.download"></script>
   <!-- jQuery-validate-js include -->
   <script src="./reference/jquery.validate.min.js.download"></script>
   <!-- Custom-js include -->
   <script src="./reference/script.js.download"></script>
   <script src="./reference/scr.js"></script>

</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration></html>