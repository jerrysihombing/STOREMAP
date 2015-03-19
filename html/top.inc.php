    
    <!-- Static navbar -->
    <div class="navbar navbar-default navbar-static-top">
      
	  <div class="container">
        
		<div class="navbar-header">          
          <a class="navbar-brand" href="#"><h1>Yogya Map Intelligence</h1></a>		  		  
        </div>
		
		<div class="collapse navbar-collapse">
          
		  <div class="navbar-right">

            <div id="bannerads-container">
              
			  <div class="bannerad">
                
				<span class="bannerad-image">
                  <a href="#" title="Toserba Yogya" target="_blank">
                    <img class="bannerad-img" src="" title="Toserba Yogya" alt="Toserba Yogya">
                  </a>
                </span>
                
				<span class="bannerad-text">
                  <a href="#">&nbsp;<small>&nbsp;</small></a>
                </span>
			  
              </div>
			  <!-- /bannerad -->
			  
            </div>
			<!-- /bannerads-container -->

          </div>
		  <!-- /navbar-right -->
		  
          
        </div>
		<!-- /collapse -->
        
        
        <div id="user-info">
            <?php
                if (!empty($user_name)) {
                    echo $user_name . " (" . $role . ")" . "&nbsp;&nbsp; | &nbsp;&nbsp;"  . "<span id='logout' onclick='location.href=\"/logout.html\"'>Logout</span>" ;
                }                
            ?>
        </div>
        
      </div>
	  <!-- /container -->
    
	</div>
	<!-- /navbar -->