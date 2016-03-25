app.config(function($routeProvider){
  //$routeProvider
  //  .when('/', {templateURL: '/lightregions_CMS/production.html',
  //  controller:'mainController',
  //  resolve:{
  //    data:function(dataSvc){        
  //      return dataSvc.promise;
  //    }
  //  }})
  })
.service('dataSvc', function($http, $q) {

    var host_root = '';
    var myData = null;
    var theme = '';

    return {
        getData: function (){
            return myData;
        },
        saveData: function() {     
            data = {
                'data' : $JSON,
                'path' : host_root + '/data/data.json'
            };

            var deferred = $q.defer();
            $http.post('save.php', data)
            .success(function(response)
            {
                deferred.resolve({
                    response: response
                });
            }).error(function(msg){
                console.log(msg);
            })
            return deferred.promise;
        },
        deleteData: function(filePath) {
            data = {
                'path': filePath
            }

            var deferred = $q.defer();
            $http.post('delete.php', data)
            .success(function(response)
            {
                deferred.resolve({
                    response: response
                });
            }).error(function(msg){
                console.log(msg);
            })
            return deferred.promise;
        },
        moveData: function(src, dest){

            data = {
                'src': src,
                'dest': dest
            }

            var deferred = $q.defer();
            $http.post('move.php', data)
            .success(function(response)
            {
                deferred.resolve({
                    response: response
                });
            }).error(function(msg){
                console.log(msg);
            })
            return deferred.promise;
        },
        getIndex: function(){
            return $index;
        },
        setIndex: function(val){
            $index = val;
        },
        getHostRoot: function(){
           return $hostRoot = host_root;
        }          
    }
})
.controller("mainController", function($scope, $http, $modal, dataSvc){       
    $scope.master = [];
    $scope.results = [];   
    $scope.selectedID = '';
    $scope.isEdit = false;
    $scope.isRemove = false;
    $scope.isInclude = false;
    $scope.isMove = false;
    $scope.isNew = false;
    $scope.isDisabled = false;
    $scope.hostRoot = dataSvc.getHostRoot();    
    $scope.dropDowns = {};
    $scope.validation = {
        isMessageShown: false,
        isSuccess: null,
        messageText: ''
    };
/********************** Initialize Bootstrap Modals *************************/

    $scope.open = function(scope, modalType, last){
        var ctrlName = 'ModalInstanceCtrl';
        var template = '';

        switch(modalType){
            case 'image':
                template = 'template/modal/image_upload.html';
            break;
            case 'video':
                template = 'template/modal/video_upload.html';
            break;            
            case 'sub':
                template = 'template/modal/sub_category.html';
            break;
            case 'subItem':
                template = 'template/modal/category.html';
            break;
            case 'error':
                template = 'template/modal/error.html';
            break;
        }

        var modalInstance = $modal.open({
            controller: ctrlName,
            transclude: true,
            templateUrl: template,
            resolve:{
                result: function(){                    
                    return scope;  
                }
            }
        })
    };


    /********************** Service Controls (Backend) Delete/Save *************************/
 
    //Delete
    $scope.remove = function(array, item){

        var _index = -1;
        _index = array.indexOf(item);

        console.log(_index)

        if(_index === -1)
            return;
        
        var deleteSrc = '';

        if (angular.isUndefined(item.url)){        
            deleteSrc = item.src;
        }
        else{
            deleteSrc = item.url;
        }

        if(deleteSrc !== ''){
            dataSvc.deleteData(deleteSrc).then(function(response){
                if(!response.response.isSuccess){
                    $scope.displayPopup(response.response.isSuccess, response.response.errorMessage);
                    return;
                }
            })
        }
            
        array.splice(_index,1);       
        $scope.$apply();
        $scope.confirmSave();        

    };  

    /********************** Edit / Manipulate Text *************************/

    $scope.og = {};
    $scope.memory = {};
    //edit
    $scope.update = function(scope, _GUID){    
        //if a control is already in edit state then force cancel this current and open new one.
        if($scope.isEdit && scope != $scope.og)
            $scope.reset($scope.memory);

        $scope.isDisabled = true;
        $scope.isEdit = true;
        $scope.selectedID = _GUID;
        $scope.og = angular.copy(scope);
        $scope.memory = scope;
    }

    $scope.setActivationState = function(scope){
        scope.isActive = !scope.isActive;   
        $scope.confirmSave();
    }

    $scope.isShow=function(GUID){
        if ($scope.isEdit)
            return GUID === $scope.selectedID;
    }

    //cancel
    $scope.reset = function(scope){
       $scope.isDisabled = false;
       $scope.isEdit = false;
       angular.copy($scope.og, scope);
    };

    //Save
    $scope.confirmSave = function(){ 
        
        dataSvc.saveData().then(function(response){
            if(response.response.isSuccess){
                $scope.displayPopup(response.response.isSuccess, 'Success!');
            }
            else{
                $scope.displayPopup(response.response.isSuccess, response.response.errorMessage);
            }
        })

        $scope.isEdit = false;
        $scope.isDisabled = false;  
        $scope.selectedID = '';
    }

    $scope.changeCategory = function(item, _scopeFrom, from){

        var itemCopy = angular.copy(item);

        var _removeAtIndex = -1;
        var appendAtIndex = 0;
        var _scopeTo = null;
        var toLower = {
            fromString: from.toLowerCase(),
            toCategory: itemCopy.itemMoveTo.category.toLowerCase(),
            toSubCategory: null
        }
        
        if(!angular.isUndefined(itemCopy.itemMoveTo.subCategory)){
            toLower.toSubCategory = itemCopy.itemMoveTo.subCategory.toLowerCase();
            dest = 'videos/' + toLower.toCategory + '/' + toLower.toSubCategory;
            src = itemCopy.src.toLowerCase();
            itemCopy.src = src.replace(toLower.fromString, toLower.toCategory + '/' + toLower.toSubCategory);
        }
        else{
            dest = 'images/' + toLower.toCategory;
            src = itemCopy.url.toLowerCase();
            itemCopy.url = src.replace(toLower.fromString, toLower.toCategory);
            itemCopy.thumb = itemCopy.thumb.toLowerCase().replace(toLower.fromString, toLower.toCategory);
        }

        angular.forEach($scope.results['sub-items'], function(category, i){
            if (category.name.toLowerCase() === toLower.toCategory){
                if(toLower.toSubCategory === null){
                    _scopeTo = category['images'];
                    appendAtIndex = category['images'].length -1;
                }
                else
                {
                    angular.forEach(category['sub-items'], function(sub, j){
                        if (sub.name.toLowerCase() === toLower.toSubCategory){
                            _scopeTo = sub['videos'];
                            appendAtIndex = sub['videos'].length -1;
                        }
                    })
                }                
            }
        })

        _removeAtIndex = _scopeFrom.indexOf(item);
        itemCopy.id = appendAtIndex + 1;

        if(_removeAtIndex !== -1){
            dataSvc.moveData(src,dest).then(function(response){
                if(response.response.isSuccess){
                    item = itemCopy;
                    _scopeFrom.splice(_removeAtIndex, 1);
                    _scopeTo.splice((appendAtIndex), 0, item);
                    $scope.confirmSave();
                    $scope.displayPopup(response.response.isSuccess, 'Success!');                    
                }
                else{
                    $scope.displayPopup(response.response.isSuccess, response.response.errorMessage);
                }
            }, function(error){
                $scope.displayPopup(false, 'The following error occured: ' + error);
            })
        }

        delete itemCopy;
        delete item.itemMoveTo;
    }

    $scope.closePopup = function(){
        $scope.validation.isMessageShown = false;
    }

    $scope.displayPopup = function(isSuccess, message){
        $scope.validation.isMessageShown = true;
        $scope.validation.isSuccess = isSuccess;
        $scope.validation.messageText = message;

        if(isSuccess){
            setTimeout(function(){
                $scope.validation.isMessageShown = false;
                $scope.$apply();
            },1000)
        }
    }

    /***********************************************************************/

    /********************** Manipulate Scope Position **********************/

    $scope.changePosition = function(scope, value, isDown){
        if (!$scope.isMove)
            return;

        var _index = scope.indexOf(value);

        if(isDown && (scope.length <= (_index + 1)))
            return;

        if(!isDown){
            if(_index === 0)
                return;
            addAtIndex = _index-1;
        }
        else
            addAtIndex = _index+1;

        scope.splice(_index, 1);
        scope.splice((addAtIndex), 0, value);  

        //reset every id by position
        angular.forEach(scope, function(items, index){
            items.id = index+1;
        });

        $scope.confirmSave();        

        timer = setTimeout(function(){
            $scope.isMove = false;
        },200);
    };

    $scope.hoverIn = function(className, $id){  

        if($scope.isEdit)
            return;

        switch (className)
        {
            case 'remove':
                $scope.isRemove = true; 
                $scope.selectedID = $id; 
            break;
            case 'move':
                $scope.isMove = true; 
                $scope.selectedID = $id; 
            break;
            case 'include':
                $scope.isRemove = true;
                $scope.isInclude = true;
                $scope.selectedID = $id; 
            break;            
        }
    }
    $scope.hoverOut = function(className){
        switch (className){
            case 'remove':
                $scope.isRemove = false; 
                if(!$scope.isEdit)
                    $scope.selectedID = ''; 
            break;
            case 'move':            
                $scope.isMove = false; 
                if(!$scope.isEdit)
                    $scope.selectedID = ''; 
            break;      
            case 'include':            
                $scope.isRemove = false; 
                $scope.isInclude = false;
                if(!$scope.isEdit)
                    $scope.selectedID = ''; 
            break;             
        }
    }

  
    $scope.fillDropDowns = function(){    

        $scope.dropDowns.Categories = {};        

        angular.forEach($scope.results['sub-items'], function(cats){              
                var subCategories = [];

                angular.forEach(cats['sub-items'], function(subs){
                    subCategories.push(subs.name);
                });            

            $scope.dropDowns.Categories[cats.name] = subCategories;
        }); 
        console.log($scope.dropDowns.Categories);
    }

    /********************** Initialize Theme *************************/

    $scope.init = function(_theme)
    {
        $current_theme_index = 0;

        switch (_theme.toLowerCase()){
            case 'photography':
                $current_theme_index = 0
            break;
            case 'production':
                $current_theme_index = 1;
            break
            default:
                $current_theme_index = 0;
            break;
        }

        $http.get($scope.hostRoot +'/data/data.json').success(function(data){

            if(data.length < 1){
                $JSON = '';
                dataSvc.Save;
            }

            $scope.master = data;

            $scope.results = data.menu[$current_theme_index];
            $scope.fillDropDowns();     
        });
    };


    /************************ WATCHES ***************************/

    $scope.$watch('master', function(newVal){     
         $JSON = $scope.master;
    }, true);

    $scope.$watch('results', function(newVal, oldVal){
        
        isNew = false;
    }, true);

}) /***************************** Everything in the modal is controlled here *********************************/
.controller('ModalInstanceCtrl', function ($scope, $modalInstance, result, dataSvc, $location, $anchorScroll){
    $scope.isEmpty;
    $scope.input = result;
    $scope.object = {};
    $scope.newItem = '';
    $scope.isSave = false;
    var old = $location.hash();
    $scope.errorMsg = '';
    $scope.hostRoot = dataSvc.getHostRoot();

    /**************** Try Save ****************/

    $scope.ok = function (item) {
        $scope.isEmpty = false;
        angular.forEach($scope.object, function(data, index){
            if(data === ''){
                $scope.isEmpty = true;
                return;
            }
        });

        if(!$scope.isSave || $scope.isEmpty)
            return;

        $scope.object.thumb = '';

        if(item === 'images')
        {
            var brokenPath = $scope.object.url.split('/');
            var filename = brokenPath[brokenPath.length-1];
            var fileMinusExt = filename.split('.')[0];
            $scope.object.thumb = $scope.object.url.replace(filename,'') + 'thumbnails/' + fileMinusExt + '_thumb.jpg';
        }
        $scope.object.id = $scope.input[item].length + 1;

        var newIndex = parseInt($scope.object.id)-1;

        $scope.input[item].push($scope.object);
        $scope.$apply();      
        dataSvc.saveData();
        uploadSuccess = false;        
        $modalInstance.dismiss('save');
        $scope.errorMsg = '';
    };

    /**************** Add Items ****************/

    $scope.createSub = function (){
        $scope.object.id = $scope.input['sub-items'].length + 1;
        $scope.object.videos = [];
        $scope.input['sub-items'].push($scope.object);
        dataSvc.saveData();
        $modalInstance.dismiss('create');
    }

    $scope.createCat = function (){

        $scope.object.id =  1;
        if ($current_theme_index === 1)
            $scope.object['sub-items'] = [];
        else
            $scope.object['images'] = [];
        $scope.input.push($scope.object);    
        dataSvc.saveData();
        $modalInstance.dismiss('create');
    }

    $scope.cancel = function () {
        uploadSuccess = false;
        $scope.errorMsg = '';

        if (!angular.isUndefined($scope.object.url) && $scope.object.url !== ''){
            dataSvc.deleteData($scope.object.url).then(function(response){
                if(response.response.isSuccess){
                    $scope.displayPopup(response.response.isSuccess, 'Success!');
                }
                else{
                    $scope.displayPopup(response.response.isSuccess, response.response.errorMessage);
                }   
            });
        }

        $modalInstance.dismiss('cancel');
    }; 

    var isShowBind = myVarWatch.watch(function(newVal) {    
        $scope.$apply(function() {
            $scope.isSave = newVal;
        });
    });

    var errorMsgBind = myVarWatch2.watch(function(newVal) {    
        $scope.$apply(function() {
            $scope.errorMsg = newVal;

        });
    });    

    $scope.$on('$destroy', isShowBind);
    $scope.$on('$destroy', errorMsgBind);
})
.controller('ShowHideCtrl', function ($scope){
    $scope.isEdit = false;
})
.directive('ngConfirmClick', function(){
    return {
        priority: 1,
        terminal: true,
        link: function (scope, element, attr) {
            var msg = attr.ngConfirmClick || "Are you sure?";
            var clickAction = attr.ngClick;
            element.bind('click',function (event) {
                if ( window.confirm(msg) ) {
                    scope.$eval(clickAction)
                }
            });
        }
    };
}).directive('ngModelOnblur', function() {
    return {
        priority: 1,
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, elm, attr, ngModelCtrl) {
            if (attr.type === 'radio' || attr.type === 'checkbox') return;
            
            elm.off('input keydown change');
            elm.on('blur', function() {
                scope.$apply(function() {
                    ngModelCtrl.$setViewValue(elm.val());
                });         
            });
        }
    };
}).directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;
            
            element.bind('change', function(){
                scope.$apply(function(){
                    modelSetter(scope, element[0].files[0]);
                });
            });
        }
    };
}]).directive('disableable', ['$parse', function ($parse) {
    return {
        restrict: 'C',
        priority: 1000000,
        require: '?ngClick',
        link: function (scope, element, attrs, ngClick) {
            if (attrs.disable){
                var disable = $parse(attrs.disable);

                element.bind('click', function (e) {
                    if (disable(scope)){
                        e.preventDefault();
                        //e.stopImmediatePropagation();
                        return false;
                    }

                    return true;
                });

                scope.$watch(disable, function (val) {
                    if (val){
                        element.addClass('disabled');
                        element.css('cursor', 'default');
                    }
                    else {
                        element.removeClass('disabled');
                        element.css('cursor', 'pointer');
                    }
                });                
            }
        }
    };
}]).directive('lrPopupMessage', function(){

    return function(scope, elm, attr){

        attr.$observe('lrPopupMessage',function(val){

            if (val === 'true'){   //if display == true
                console.log('remove hidden')
                elm.removeClass('hidden');
            }
            else{
                console.log('add hidden')
               elm.addClass('hidden');
            }
        })
    }
    
});
