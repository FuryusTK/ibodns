<?php
echo "
    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js\"></script>
    <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js\"></script>
</div>

<!-- Footer -->
<footer class=\"sticky-footer\">
    <center></center>
    <div class=\"container\">
        <div class=\"copyright text-center\">
            <span><a href=\"\" target=\"_blank\">&#169; PAINEL IBO<sup>REVENDA</sup></a></span>
        </div>
    </div>
</footer>
<!-- End of Footer -->

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class=\"scroll-to-top rounded\" href=\"#page-top\">
    <i class=\"fas fa-angle-up\"></i>
</a>

<!-- Bootstrap core JavaScript-->
<script src=\"vendor/jquery/jquery.min.js\"></script>
<script src=\"vendor/bootstrap/js/bootstrap.bundle.min.js\"></script>

<!-- Core plugin JavaScript-->
<script src=\"vendor/jquery-easing/jquery.easing.min.js\"></script>

<!-- Custom scripts for all pages-->
<script src=\"js/sb-admin-2.min.js\"></script>
<script src=\"js/jquery.datetimepicker.js\"></script>

<script>
    var today = new Date();
    var date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
    var time = today.getHours() + \":\" + today.getMinutes() + \":\" + today.getSeconds();
    var dateTime = date + ' ' + time;

    \$('#datetimepicker').datetimepicker({
        //value:dateTime, 
        step: 30,
        format: 'Y-m-d H:i:s',
    });

    \$(document).ready(function() {
        \$(\"#flash-msg\").delay(3000).fadeOut(\"slow\");
    });
</script>
";
?>