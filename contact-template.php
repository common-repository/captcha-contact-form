<?php 

require_once('environment.php');

?>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<!-- Latest compiled and minified JavaScript -->

<style>
		#firstRowContact {
			flex-direction: row;
		}
	@media screen and (max-width: 768px) {
		
		#firstRowContact {
			flex-direction: column;
		}
	}
	.btn-primary {
		background-color: rgba(0,0,0,0.2);
		border: none;
		outline: none;
		color: black;
	}
	.btn-primary:hover {
		background-color: rgba(0,0,0,0.4);
		cursor: pointer;
		color: white;
	}
@keyframes spinner {
        0% {
          transform: rotate(0deg);
        }
        100% {
          transform: rotate(360deg);
        }
      }
      #spin {
		  display: none;
		  z-index: 9999;
        animation: 1.5s linear infinite spinner;
        animation-play-state: inherit;
        border: solid 5px #cfd0d1;
        border-bottom-color: #1c87c9;
        border-radius: 50%;
        height: 32px;
		  opacity: 0.8;
        width: 32px;
        top: 10%;
        left: 10%;
        will-change: transform;
      }

</style>

<script>
    function submitCaptcha(token) {
        var sendButton = document.getElementById("submitButtonForm");
        sendButton.style.pointerEvents = "auto";
        sendButton.classList.add("btn-primary");
    }
    
    function submitForm() {  
var spinned = document.getElementById("spin");
		spinned.style.display = "block";
        var name = document.getElementById("JDC_F_name").value;
        var email = document.getElementById("JDC_F_email").value;
        var subject = document.getElementById("JDC_F_subject").value;
        var message = document.getElementById("JDC_F_message").value;
    
        // xmlhttp request to server
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open('POST', "<?php echo esc_url(admin_url( 'admin-ajax.php' )); ?>", true);
        xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

        xmlhttp.onreadystatechange = function () {
            if (this.readyState==4 && this.status==200) {
                // parse the json string into a javascript object
                var statusElements = document.getElementsByClassName("JDC_F_status");
                for (var el in statusElements) {
					spinned.style.display = "none";
                    statusElements[el].innerText =  JSON.parse(this.responseText)[0];
                }
                console.log(JSON.parse(this.responseText)[1]);
            }
        };
        // send the info to php
        var captchaResponse = grecaptcha.getResponse();
        var contactFormData = name + "/////" + email + "/////" + subject + "/////" + message + "/////" + captchaResponse;
        xmlhttp.send('action=contactFormFunction&contactFormData=' + contactFormData);
    }
    function recaptchaExpired() {
        var sendButton = document.getElementById("submitButtonForm");
        sendButton.style.pointerEvents = "none";
        sendButton.classList.remove("btn-primary");
    }
// })
</script>

<section style="display: flex; flex-direction: column; gap: 10px 10px;">

    <?php
        $site_key = $_ENV['SITE_KEY'];
        $secret_key = $_ENV['SECRET_KEY'];
    ?>

			<div id="firstRowContact" id="changeThisWidth" style="display: flex; gap: 10px 10px;">
				<input style="width: 100%;" type="text" placeholder="Your name" id="JDC_F_name" name="name" class="form-control formBack">
				<input style="width: 100%;" type="text" id="JDC_F_email" placeholder="Your email" name="email" class="form-control formBack">
			</div>
			
            
			
           <input type="text" placeholder="Subject" id="JDC_F_subject" name="subject" class="form-control formBack">
         	<textarea placeholder="Your message" type="text" id="JDC_F_message" name="message" rows="2" class="form-control md-textarea formBack"></textarea>
																																	
             <a style="width: 100%" class="g-recaptcha" data-sitekey=<?php echo esc_attr($site_key); ?> data-callback="submitCaptcha" data-expired-callback="recaptchaExpired"></a> 
																																	<div class="fifthRowContact" style="display: flex;">
				                <button class="btn" id="submitButtonForm" style="pointer-events: none; background-color; grey;" onclick="submitForm()">Send</button>
<div id="spin" style="margin-left: 10px;"></div><div class="JDC_F_status" style="margin-top: 7px; color: white; display: inline-block; margin-left: 10px; color: #A9A9A9;"></div>
</div>

		 
    
</section>

