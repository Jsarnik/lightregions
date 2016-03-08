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
  <title>Light Regions CMS - Production</title>
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

<style type="text/css">
.ng-modal-overlay {
  /* A dark translucent div that covers the whole screen */
  position:fixed;
  z-index:9999;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background-color:#000000;
  opacity: 0.8;
}
.ng-modal-dialog {
  /* A centered div above the overlay with a box shadow. */
  z-index:10000;
  position: absolute;
  width: 50%; /* Default */

  /* Center the dialog */
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  -webkit-transform: translate(-50%, -50%);
  -moz-transform: translate(-50%, -50%);

  background-color: #fff;
  box-shadow: 4px 4px 80px #000;
}
.ng-modal-dialog-content {
  padding:10px;
  text-align: left;
}
.ng-modal-close {
  position: absolute;
  top: 3px;
  right: 5px;
  padding: 5px;
  cursor: pointer;
  font-size: 120%;
  display: inline-block;
  font-weight: bold;
  font-family: 'arial', 'sans-serif';
}

#bar_blank {
  border: solid 1px #000;
  height: 20px;
  width: 300px;
}
 
#bar_color {
  background-color: #004FCC;
  height: 20px;
  width: 0px;
}
 
#bar_blank, #hidden_iframe {
  display: none;
}

.show{
  display: block;
}

#upload_frame{
  display: none;
}

</style>
</head>
<body ng-app="CMSLightRegionsApp">
    <div class='banner'>
        <div class='container'>
          <div id="header-content">
            <h1>Production</h1>
            <div id="header-links">
              <span><a href="photography.php">Photography</a></span> <span> | </span> <span><a href="logout.php">Logout</a></span>
              <br/>
              <div id="user_name"></div>
            </div>
          </div>
        </div>
    </div>
  <div class="container main-frame" ng-controller="mainController" ng-init="init('production')">
    <div class="search-box row-fluid form-inline">
      <label>Category: </label>
<input id="category-filter" type="text" ng-model="filterCategory"/>
<label>Sub Category: </label>
<input id="sub-category-filter" type="text" ng-model="filterSubCategory"/>
<label>Video: </label>
<input type="text" id="video-filter" ng-model="filterVideo"/>
    </div>
        <div class="results-top"></div>
        <div class="results-container">
            <ul class="repeater-list">
<div class="button-container">
      <div class="button add" ng-click="open(results['sub-items'], 'subItem')" type="button">Category +</div>
</div>
      <li id="category-{{$index}}" class="category-container" ng-repeat="subItems in results['sub-items'] | filter: filterCategory  track by $index">

<div class="info-overlay" ng-class="{'remove' : isRemove && selectedID == 'category-' + $index,'move' : isMove && selectedID == 'category-'+ $index}"></div>

                <div id="subItems-{{$index}}" class="category-header">
                    
<div class="info-overlay"  ng-class="{'edit' : isEdit && selectedID == 'subItem-' + $index,'remove' : isInclude && selectedID == 'subItem-'+ $index}"></div>            		
                    <div class="align-header-buttons">	

<div ng-mouseover="hoverIn('remove', 'category-' + $index);" class="disableable" disable="isDisabled" ng-mouseleave="hoverOut('remove');">
                          <div class="button delete" ng-click="remove(results['sub-items'], subItems);" ng-confirm-click="Are you sure you want to delete {{subItems.name}}?">X</div>
</div>

<div ng-mouseover="hoverIn('move', 'category-' + $index);" ng-mouseleave="hoverOut('move');">
                                  <div class="button up" ng-click="changePosition(results['sub-items'],$index, subItems, false)">&#9650;</div>
</div>
<div ng-mouseover="hoverIn('move', 'category-' + $index);" ng-mouseleave="hoverOut('move');">
                                  <div class="button down" ng-click="changePosition(results['sub-items'],$index, subItems, true)">&#9660;</div>
</div>
                        <div ng-hide="isShow('subItem-'+$index)">
                            <span>|</span>
                            <span>{{subItems.name.toUpperCase()}}</span><span> ( {{subItems['sub-items'].length}} )  |</span>
                            <a href="javascript:void(0)" ng-click="update(subItems, 'subItem-' + $index);">Edit</a>
                        </div>
                        <div ng-show="isShow('subItem-'+$index)">
                            <input ng-model="subItems.name"/>
                            <a href="javascript:void(0)" ng-click="confirmSave();">Save</a> |
                            <a href="javascript:void(0)" ng-click="reset(subItems);">Cancel</a>
                        </div>
<div class="button-container">
                    <div class="button add" ng-click="open(subItems, 'sub')" type="button">Sub +</div>                 
</div>
                    </div>
                </div>
                <ul>
                    <li ng-repeat="sub in subItems['sub-items'] | filter: filterSubCategory  track by $index">

        <div class="info-overlay" ng-class="{'remove' : isRemove && selectedID == 'subItem-' + $parent.$index + '-' + $index, 'edit' : isEdit && selectedID == 'subItem-'  + $parent.$index + '-' + $index, 'new' : isNew && selectedID == 'subItem-' + $parent.$index + '-' + $index, 'move' : isMove && selectedID == 'subItem-' + $parent.$index + '-' + $index}"></div>
                                <div id="subItem-{{$parent.$index}}-{{$index}}" class="sub-category-header" ng-class="{'edit' : isEdit && selectedID == 'subItem-' + $index,'remove' : isRemove && selectedID == 'subItem-' + $index, 'move' : isMove && selectedID == 'subItem-' + $index}">

        <div class="info-overlay" ng-class="{'remove' : isRemove && selectedID == 'subItem-' + $parent.$index + '-' + $index, 'edit' : isEdit && selectedID == 'subItem-'  + $parent.$index + '-' + $index, 'new' : isNew && selectedID == 'subItem-' + $parent.$index + '-' + $index, 'move' : isMove && selectedID == 'subItem-' + $parent.$index + '-' + $index}"></div>
                                
        <div class="align-header-buttons">
                <div>
<div ng-mouseover="hoverIn('remove', 'subItem-' + $parent.$index + '-' + $index);" class="disableable" disable="isDisabled" ng-mouseleave="hoverOut('remove');">
                         <div class="button delete" ng-click="remove(subItems['sub-items'], sub);" ng-confirm-click="Are you sure you want to delete {{sub.name}}?">X</div>
</div>                   
                  <div ng-mouseover="hoverIn('move', 'subItem-' + $parent.$index + '-' + $index);" class="disableable" disable="isDisabled" ng-mouseleave="hoverOut('move');">
                                <div class="button up" ng-click="changePosition(subItems['sub-items'],$index, sub, false)">&#9650;</div>
</div>
<div ng-mouseover="hoverIn('move', 'subItem-' + $parent.$index + '-' + $index);" class="disableable" disable="isDisabled" ng-mouseleave="hoverOut('move');">
                                <div class="button down" ng-click="changePosition(subItems['sub-items'],$index, sub, true)">&#9660;</div>
</div>                        
                      <div ng-hide="isShow('subItem-' + $parent.$index + '-' + $index)">
                          <span>|</span>
                          <span>{{sub.name.toUpperCase()}} </span><span> ( {{sub.videos.length}} )</span> |
                          <a href="javascript:void(0)" ng-click="update(sub, 'subItem-' + $parent.$index + '-' + $index);">Edit</a>
                      </div>
                      <div ng-show="isShow('subItem-' + $parent.$index + '-' + $index)">
                          <input ng-model="sub.name"/>
                          <a href="javascript:void(0)" ng-click="confirmSave();">Save</a> |
                          <a href="javascript:void(0)" ng-click="reset(sub);">Cancel</a>
                      </div>
                      </div>

                     <div class="button-container">                   
<div class="button add" ng-click="open(sub, 'video')" type="button">Video +</div>
                  </div>
                    </div>

                        </div>
                        <ul class="video">
                          <li id="video-{{$parent.$parent.$index}}-{{$parent.$index}}-{{$index}}" ng-class="{'item-in-delete' : isDelete}" ng-repeat="video in sub.videos | filter: filterVideo  track by $index">
                        <div ng-mouseover="hoverIn('remove', 'video-' + $parent.$parent.$index + '-' + $parent.$index + '-' + $index);" ng-mouseleave="hoverOut('remove');">
                        <div class="button delete item" ng-click="remove(sub.videos, video);" ng-confirm-click="Are you sure you want to delete {{video.title}}?">X</div>
                        </div>
                        <div class="info-overlay" ng-class="{'remove' : isRemove && selectedID ==  'video-' + $parent.$parent.$index + '-' + $parent.$index + '-' + $index, 'edit' : isEdit && selectedID.indexOf($parent.$parent.$index + '-' +  $parent.$index + '-' + $index) >= 0, 'new' : isNew && selectedID ==  'video-'  + $parent.$parent.$index + '-' +  $parent.$index + '-' + $index, 'move' : isMove && selectedID ==  'video-'   + $parent.$parent.$index + '-' +  $parent.$index + '-' + $index}"></div>    
                        <div class="info">
                                <div class="video-container">
                                    <table>
                                  <span class="item_status" ng-class="{'active' : video.isActive}">Inactive</span>                                    
                                        <tr><td><span>TITLE: </span></td>
                                        <td class="td-large">
                                            <div>
                                                <div ng-hide="isShow(title + '-'  + $parent.$parent.$index + '-' + $parent.$index + '-' + $index)">
                                                    {{video.title.toUpperCase()}}
                                                     <span ng-class="{'inactive' : !video.isActive}" > | 
                                                      <a href="javascript:void(0)" ng-click="update(video,title + '-' + $parent.$parent.$index + '-' + $parent.$index + '-' + $index); changeClass();">Edit</a>
                                                      </span>
                                                </div>
                                                <div ng-show="isShow(title + '-'  + $parent.$parent.$index + '-' + $parent.$index + '-' + $index)">
                                                    <input ng-model="video.title"/>
                                                    <a href="javascript:void(0)" ng-click="confirmSave();">Save</a> |
                                                    <a href="javascript:void(0)" ng-click="reset(video);">Cancel</a>
                                                </div>

                                            </div>
                                        </td></tr>
                                        <tr><td><span>SOURCE: </span></td>
                                            <td class="td-large">
                                            <div>
                                                <div ng-hide="isShow('src-'  + $parent.$parent.$index + '-' +  $parent.$index + '-' + $index)">
                                                    {{hostRoot}}/{{video.src}}
                                                    (<a target="_blank" href="{{hostRoot}}/{{video.src}}">view</a>)
                                                    <span ng-class="{'inactive' : !video.isActive}" > | 
                                                      <a href="javascript:void(0)" ng-click="update(video,'src-'  + $parent.$parent.$index + '-' + $parent.$index + '-' + $index);">Edit</a>
                                                    </span>
                                                </div>
                                                <div ng-show="isShow('src-'  + $parent.$parent.$index + '-' +   $parent.$index + '-' + $index)">
                                                    <input ng-model="video.src"/>
                                                    <a href="javascript:void(0)" ng-click="confirmSave();">Save</a> |
                                                    <a href="javascript:void(0)" ng-click="reset(video);">Cancel</a>
                                                </div>
                                            </div>
                                            </td></tr>
                                        <tr><td><span>THUMB: </span></td>
                                            <td class="td-large">
                                                 <div>
                                                <div ng-hide="isShow('thumb-'  + $parent.$parent.$index + '-' +  $parent.$index + '-' + $index)">
                                                    {{hostRoot}}/{{video.thumb}}
                                                    <span ng-class="{'inactive' : !video.isActive}" > | 
                                                      <a href="javascript:void(0)" ng-click="update(video,'thumb-'  + $parent.$parent.$index + '-' + $parent.$index + '-' + $index);">Edit</a>
                                                      </span>
                                                </div>
                                                <div ng-show="isShow('thumb-'  + $parent.$parent.$index + '-' +  $parent.$index + '-' + $index)">
                                                    <input ng-model="video.thumb"/>
                                                    <a href="javascript:void(0)" ng-click="confirmSave();">Save</a> |
                                                    <a href="javascript:void(0)" ng-click="reset(video);">Cancel</a>
                                                </div>
                                            </div>
                                            </td></tr>
                                        <tr><td><span>VIMEO: </span></td>
                                            <td class="td-large">
                                            <div>
                                                <div ng-hide="isShow('vimeo-'  + $parent.$parent.$index + '-' +  $parent.$index + '-' + $index)">
                                                    {{video.vimeo}}
                                                    <span ng-class="{'inactive' : !video.isActive}" > | 
                                                      <a href="javascript:void(0)" ng-click="update(video,'vimeo-'  + $parent.$parent.$index + '-' + $parent.$index + '-' + $index);">Edit</a>
                                                      </span>
                                                </div>
                                                 <div ng-show="isShow('vimeo-'  + $parent.$parent.$index + '-' + $parent.$index + '-' + $index)">
                                                    <input ng-model="video.vimeo"/>
                                                    <a href="javascript:void(0)" ng-click="confirmSave();">Save</a> |
                                                    <a href="javascript:void(0)" ng-click="reset(video);">Cancel</a>
                                                </div>
                                            </div>
                                        </td></tr>
                                        <tr><td><span>YOUTUBE: </span></td>
                                            <td class="td-large">
                                            <div>
                                                <div ng-hide="isShow('youtube-'  + $parent.$parent.$index + '-'+  $parent.$index + '-' + $index)">
                                                    {{video.youtubeid}}
                                                    <span ng-class="{'inactive' : !video.isActive}" > | 
                                                    <a href="javascript:void(0)" ng-click="update(video,'youtube-'  + $parent.$parent.$index + '-'+ $parent.$index + '-' + $index);">Edit</a>
                                                    </span>
                                                </div>
                                                <div ng-show="isShow('youtube-'   + $parent.$parent.$index + '-'+  $parent.$index + '-' + $index)">
                                                    <input ng-model="video.youtubeid"/>
                                                    <a href="javascript:void(0)" ng-click="confirmSave();">Save</a> |
                                                    <a href="javascript:void(0)" ng-click="reset(video);">Cancel</a>
                                                </div>
                                            </div>
                                            </td></tr>
                                        <tr><td><span>ACTIVE: </span></td>
                                            <td class="td-large">
                                            <div>
                                                <input type="checkbox" ng-model="video.isActive" ng-click="setActivationState(video);">
                                              </div>

                                        </td></tr>
                                    </table>
                                <div class="button-container">
                                                                  <div style="position:relative; height:26px; display:block;"></div>
                                  <div ng-mouseover="hoverIn('move', 'video-'  + $parent.$parent.$index + '-' + $parent.$index + '-' + $index);" class="disableable" disable="isDisabled" ng-mouseleave="hoverOut('move');"><button class="button up" ng-click="changePosition(sub.videos,$index, video,false)">&#9650;</button></div>                                  
                                  <div ng-mouseover="hoverIn('move', 'video-'  + $parent.$parent.$index +  '-' +$parent.$index + '-' + $index);" class="disableable" disable="isDisabled" ng-mouseleave="hoverOut('move');"><button class="button down" ng-click="changePosition(sub.videos,$index, video,true)">&#9660;</button></div>
                                  <div style="position:relative; height:26px; display:block;"></div>
                                </div>
                                      
                                      
                                  </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
      
      </ul>
        </div>
        
  </div>

<iframe id="my_iframe" name="upload_target" src="" style="width:0;height:0;border:0px solid #fff;"></iframe>     

<script src="js/fileUpload.js"></script>

</body>
</html>                                