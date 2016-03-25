<?php  

session_start();
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

if (!isset($_SESSION['current_user']['login_user_id'])) {
  header("Location: login.php");
}

$current_user = strtoupper($_SESSION['current_user']['login_username']);

//get unique id 
$up_id = uniqid();  
?> 

<!DOCTYPE html>
<html>
<head>
  <title>Light Regions CMS - Photography</title>
  <!-- INCLUDE REQUIRED THIRD PARTY LIBRARY JAVASCRIPT AND CSS -->
  <!--<script type="text/javascript" src="js/angular.min.js"></script>-->
      <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.4/angular.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.1/angular-route.min.js"></script>
  <script type="text/javascript" src="js/cms-app/app.js"></script>
  <script type="text/javascript" src="js/cms-app/controllers/mainController.js"></script>
  <script src="js/ui-bootstrap-0.12.1.min.js"></script>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/bootstrap-responsive.min.css">

<script type="text/javascript">

$(document).ready(function(){

  var current_user = '<?php echo $current_user ?>';
  $('#user_name').html(current_user);

});
</script>


</style>
</head>
<body ng-app="CMSLightRegionsApp">
    <div class='banner'>
      <div class='container'>
        <div id="header-content">
          <h1>Photography</h1>
          <div id="header-links">
            <span><a href="production.php">Production</a></span> <span> | </span> <span><a href="logout.php">Logout</a></span>
                          <br/>
            <div id="user_name"></div>
          </div>
        </div>
      </div>
    </div>
  <div ng-cloak class="container main-frame" ng-controller="mainController" ng-init="init('photography')">
    
    <div class="popup-message hidden" ng-class="validation.isSuccess ? 'success' : 'error'" lr-popup-message="{{validation.isMessageShown}}">
      <div class="close-button" ng-click="closePopup()">&#x2716;</div>
      <span>{{validation.messageText}}</span>
    </div>
    
    <div class="search-box row-fluid form-inline">
      <label>Category: </label>
<input id="category-filter" type="text" ng-model="filterCategory"/>
<label>Photo: </label>
<input id="item" type="text" ng-model="filterPhoto"/>
    </div>
        <div class="results-top"></div>
        <div class="results-container">
            <ul class="repeater-list">
      <div class="button add" ng-click="open(results['sub-items'], 'subItem')" type="button">Category +</div>
      <li id="category-{{$index}}" class="category-container" ng-repeat="subItems in results['sub-items'] | filter: filterCategory track by $index">

        <div class="info-overlay" ng-class="{'include' : isInclude && selectedID == 'subItem-' + $parent.$index + '-' +  $index}"></div>

                <div id="subItem-{{$parent.$index}}-{{$index}}" class="category-header">
            				<div class="info-overlay"  ng-class="{'edit' : isEdit && selectedID == 'subItem-' + $parent.$index +  '-' + $index,'remove' : isInclude && selectedID == 'subItem-' + $parent.$index +  '-' + $index, 'move' : isMove && selectedID == 'subItem-' + $parent.$index +  '-' + $index}"></div>            		
            		<div class="align-header-buttons">	
                              <div ng-mouseover="hoverIn('include', 'subItem-' + $parent.$index +  '-' + $index);" class="button delete disableable" disable="isDisabled" ng-mouseleave="hoverOut('include');" >
                              	<div class="disableable" ng-click="remove(results['sub-items'], subItems);" ng-confirm-click="*WARNING* Deleting category ({{subItems.name}}) will delete all of it's images too?">&#9747;</div>
                              </div>

                              <div ng-mouseover="hoverIn('move', 'subItem-' + $parent.$index +  '-' + $index);" class="button up disableable" disable="isDisabled" ng-mouseleave="hoverOut('move');" ng-click="changePosition(results['sub-items'], subItems,false)">&#9650;</div>

                            
                              <div ng-mouseover="hoverIn('move', 'subItem-' + $parent.$index +  '-' + $index);" class="button down disableable" disable="isDisabled" ng-mouseleave="hoverOut('move');" ng-click="changePosition(results['sub-items'], subItems,true)">&#9660;</div>

                        <div ng-hide="isShow('subItem-'+ $parent.$index +  '-' +$index)">
                            <span>|</span>
                            <span>{{subItems.name.toUpperCase()}} </span><span>( {{subItems.images.length}} )</span>                            
                            <a class="linkbutton" class="edit" href="javascript:void(0)" ng-click="update(subItems, 'subItem-'+ $parent.$index +  '-' +$index);">Edit</a>
                            </div>
                        <div ng-show="isShow('subItem-'+ $parent.$index +  '-' +$index)">
                            <input ng-model="subItems.name"/>
                            <a class="linkbutton" href="javascript:void(0)" ng-click="confirmSave();">Save</a> |
                            <a class="linkbutton" href="javascript:void(0)" ng-click="reset(subItems);">Cancel</a>
                        </div>
                    <div class="button-container">
                    <div class="button add disableable" disable="isDisabled" ng-click="open(subItems, 'image')" type="button">Image +</div>
           		</div>
                </div>
                </div>
                    <ul class="image">
                        <li ng-class="{'light':$even,'dark':$odd}" id="image-{{$parent.$index}}-{{$index}}" ng-repeat="image in subItems.images | filter: filterPhoto track by $index">       
                        
                        <div ng-mouseover="hoverIn('remove', 'image-' + $parent.$index + '-' + $index);" ng-mouseleave="hoverOut('remove');" class="button delete item">
                        	<div class="disableable" ng-click="remove(subItems.images, image);" ng-confirm-click="Are you sure you want to delete {{image.title}}?">&#9747;</div>
                        </div>
                        
                        <div class="info-overlay" ng-class="{'remove' : isRemove && selectedID == 'image-' + $parent.$index + '-' + $index, 'edit' : isEdit && selectedID == 'image-' + $parent.$index + '-' + $index, 'new' : isNew && selectedID == 'image-' + $parent.$index + '-' + $index, 'move' : isMove && selectedID == 'image-' + $parent.$index + '-' + $index}"></div>
                        <div class="info" ng-class="{'last':$last}">                                       
                            <div class="image-container">
                              <div class="images-thumb"><img ng-src="{{hostRoot}}{{image.thumb}}" /></div>
                                <table>
                                  <span class="item_status" ng-class="{'active' : image.isActive}">Inactive</span>
                                    <tr><td><span>TITLE:</span></td>
                                    <td class="td-large">
                                        <div>
                                            <div ng-hide="isShow('image-' + $parent.$index + '-' + $index)">
                                                {{image.title.toUpperCase()}}
                                                <span ng-class="{'inactive' : !image.isActive}" > | 
                                                  <a class="linkbutton" class="edit" href="javascript:void(0)" ng-click="update(image, 'image-' + $parent.$index + '-' + $index);">Edit</a>
                                                </span>
                                            </div>
                                            <div ng-show="isShow('image-' + $parent.$index + '-' + $index)">
                                                <input ng-model="image.title"/>
                                                <a class="linkbutton" href="javascript:void(0)" ng-click="confirmSave();">Save</a> |
                                                <a class="linkbutton" href="javascript:void(0)" ng-click="reset(image);">Cancel</a>
                                            </div>
                                        </div>
                                    </td></tr>
                                    <tr><td><span>SOURCE: </span></td>
                                        <td class="td-large">
                                            <div>
                                                <a target="_blank" href="{{hostRoot}}{{image.url}}">{{hostRoot}}{{image.url}}</a>                                            
                                            </div>
                                        </td></tr>    
                                        <tr>
                                          <td>
                                            <span>ACTIVE: </span>
                                          </td>
                                          <td class="td-large">
                                            <div>
                                              <input type="checkbox" ng-model="image.isActive" ng-click="setActivationState(image);">
                                            </div>
                                          </td>
                                        </tr>
                                          <tr>
                                          <td>
                                            <span>Move To: </span>
                                          </td>
                                          <td class="td-large">
                                            <div>
                                              <span>Category:</span>
                                              <select class="dropDown" ng-model="image.itemMoveTo.category">
                                                <option ng-if="key != subItems.name" ng-repeat="(key, value) in dropDowns.Categories" value="{{key}}">{{key}}</option>                                              
                                              </select>
                                            </div>
                                          </td>
                                          <td ng-show="image.itemMoveTo.category != null">
                                            <a class="linkbutton" href="javascript:void(0)" ng-click="changeCategory(image, subItems.images, subItems.name)" ng-confirm-click="Are you sure you want to move {{image.title}} from {{subItems.name}} to {{image.itemMoveTo.category}}?"> | Move</a>
                                          </td>
                                        </tr>                                        
                                </table>
                                <div class="button-container">
                                  <div style="position:relative; height:26px; display:block;"></div>
                                  <div ng-mouseover="hoverIn('move', 'image-' + $parent.$index + '-' + $index);" class="button up disableable" disable="isDisabled" ng-mouseleave="hoverOut('move');" ng-click="changePosition(subItems.images, image,false)">&#9650;</div>                                  
                                  <div ng-mouseover="hoverIn('move', 'image-' + $parent.$index + '-' + $index);" class="button down disableable" disable="isDisabled" ng-mouseleave="hoverOut('move');" ng-click="changePosition(subItems.images, image,true)">&#9660;</div>
                                  <div style="position:relative; height:26px; display:block;"></div>
                                </div>
                                  
                            </div>
                          </div>
                        </li>
                    </ul>
                    <div id="scrollBottom"></div>
            </li>
      
      </ul>
        </div>
      
  </div>
            
<iframe id="my_iframe" name="upload_target" src="" style="width:0;height:0;border:0px solid #fff;"></iframe>

<script src="js/fileUpload.js"></script>









</body>
</html>