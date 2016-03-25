
app.config(['$routeProvider', '$locationProvider',
  function($routeProvider, $locationProvider) {
    $routeProvider
    .when('/index.html',{
	 	templateUrl: 'production.html',

    }).when('/photography', {
        templateUrl: 'photography.html',
        controller: 'ContentCtrl'

    }).when('/photography/:catName', {
        templateUrl: 'photography.html',
        controller: 'ContentCtrl'

    }).when('/photography/:catName/:itemName', {
        templateUrl: 'photography.html',
        controller: 'ContentCtrl'

    }).when('/production', {
        templateUrl: 'production.html',
        controller: 'ContentCtrl'

    }).when('/production/:catName', {
        templateUrl: 'production.html',
        controller: 'ContentCtrl'

    }).when('/production/:catName/:subName', {
        templateUrl: 'production.html',
        controller: 'ContentCtrl'

    }).when('/production/:catName/:subName/:itemName', {
        templateUrl: 'production.html',
        controller: 'ContentCtrl'

    }).when('/about', {
        templateUrl: 'about.html',
        controller: 'AboutCtrl'

    }).when('/preview', {
        templateUrl: 'preview.html',
        controller: 'previewCtrl'
    });

	$routeProvider.otherwise({redirectTo: '/preview', controller: 'previewCtrl'});
    //$locationProvider.html5Mode(true);
}])
.factory('dataSvc', function($http, $location, $q) {

    var theme = 'production';
    var dataCol = [];

    return {
        initGetData: function(theme){
            var deferred = $q.defer();
            $http.get('/data/data.json')
            .success(function(data){
                deferred.resolve({
                    theme: data.menu
                });                            
            }).error(function(msg,code){
                deferred.reject(msg);
            });
        return deferred.promise;
        },        
        getTheme: function (){
            if($location.path().split("/")[1] !== null && !angular.isUndefined($location.path().split("/")[1]))
                theme = $location.path().split("/")[1].toLowerCase();
            return theme;
        }       
    }
})
.service('analytics', function($window) {
    return {
        pageLoad: function (scope, locationPath, title){
    		$window.ga('send', {'hitType' : 'pageview', 'page': locationPath, 'title': title});
        },
		pageClick: function (action, label){
    		$window.ga('send', 'event', action, label);
        }      
    }
})
.controller("MainCtrl", function($scope, $http, dataSvc, $routeParams, $location, analytics, $rootScope){
	$scope.isMobile = true;
    $scope.AllData;
    $scope.Collection = {};
    $scope.Collection.Theme = '';
    $scope.Collection.Category = {};
    $scope.Collection.Category.Name = '';
    $scope.Collection.Category.Index = 0;
    $scope.Collection.SubCategory = {}
    $scope.Collection.SubCategory.Name = '';
    $scope.Collection.SubCategory.Index = 0;    
    $scope.Collection.Item = {};
    $scope.Collection.Item.Name = '';
    $scope.Collection.Item.Index = 0;
    $scope.Collection.Data = [];
    $scope.Collection.Initialized = false;
    $scope.DataIndex = 0;
    $scope.ActiveItem = {};
    $scope.Theme = null;
    $scope.isMobileNavExp = false;
    
    $scope.headerLinks = [
        {title: "Production", url : "#production"},
        {title: "Photography", url : "#photography"},
        {title: "About", url : "#about"},
        {title: "Contact", url : "mailto:info@lightregions.com"}
    ]       

    $scope.$on('$routeChangeSuccess', function() {        

        $scope.Collection.Category.Name = angular.isUndefined($routeParams.catName) ? '' : $routeParams.catName;
        $scope.Collection.SubCategory.Name = angular.isUndefined($routeParams.subName) ? '' : $routeParams.subName;
        $scope.Collection.Item.Name = angular.isUndefined($routeParams.itemName) ? '' : $routeParams.itemName;

        $scope.Theme = dataSvc.getTheme();
        $scope.isMobileNavExp = false;       

         switch($scope.Theme){
            case 'photography':
                $scope.DataIndex = 0;            
            break;
            case 'production':
                $scope.DataIndex = 1;
            break;            
        } 

        $scope.PageRouteTitleAnalytics();

    });    

    $scope.PageRouteTitleAnalytics = function(){
            var trackTitleString = '- Home';

            if($scope.Collection.Category.Name !== '')
                trackTitleString = trackTitleString + ' - ' + $scope.Collection.Category.Name;

            if($scope.Collection.SubCategory.Name !== '')
                trackTitleString = trackTitleString + ' - ' + $scope.Collection.SubCategory.Name;

            if($scope.Collection.Item.Name !== '')
                trackTitleString = trackTitleString + ' - ' + $scope.Collection.Item.Name;                         

            analytics.pageLoad($scope,$location.absUrl(),'Light Regions' + trackTitleString);
    }

    
    var init = function()
    {
        if (!$scope.Collection.Initialized){
            dataSvc.initGetData().then(function(data) {     
                $scope.AllData = data.theme;                                             
            });
        }
        return true;
	}

    $scope.init = init();

    /************************ WATCHES ***************************/

    $scope.getWidth = function() {
        return $(window).width();
    };

    $scope.$watch($scope.getWidth, function(newVal) {        
        if(newVal <= 768)
        	$scope.isMobile = true;
        else
        	$scope.isMobile = false;
    });
    window.onresize = function(){
        $scope.$apply();
    }

})
.controller("AboutCtrl", function($scope){

})
.controller("ContentCtrl", function($scope, $http, dataSvc, $routeParams, $location, analytics, $rootScope){

    $scope.RedirectURL = function(_item){

        var url = '/'+$scope.Collection.Theme.toLowerCase();

        if($scope.Collection.Category.Name !== '')
            url = url + '/' + $scope.Collection.Category.Name;

        if ($scope.Collection.SubCategory.Name !== '')
            url = url + '/' + $scope.Collection.SubCategory.Name;

        if (_item !== '')
            url = url + '/' + _item;

        return url;

    }

    $scope.RenderView = function(){

        if ($scope.Collection.Item.Index === -1 || $scope.Collection.Item.Name === ''){
            $scope.Collection.Item.Index = 0;
            $location.path($scope.RedirectURL($scope.Collection.Data[$scope.Collection.Item.Index].title));
        }
        
        $scope.ActiveItem = $scope.Collection.Data[$scope.Collection.Item.Index];      
        $scope.ActiveItem.isLoaded = false;  
    }

    $scope.FillCollection = function(){

        var collection = [];                         
        var _themeIndex = $scope.DataIndex;
        //Default Selection
        if ($scope.Collection.Category.Name == ''){
            $scope.Collection.Category.Name = $scope.AllData[_themeIndex]['sub-items'][0].name;
        }

        switch(_themeIndex){
            case 0:
                angular.forEach($scope.AllData[_themeIndex]['sub-items'], function(category, categoryIndex) {
                    if(category.name.toLowerCase() === $scope.Collection.Category.Name.toLowerCase()){    
                        $scope.Collection.Category.Index = categoryIndex;     
                        angular.forEach(category['images'], function(image, imageIndex) {

                            if(image.isActive){
                                collection.push(image);
                                
                                if(image.title.toLowerCase() === $scope.Collection.Item.Name.toLowerCase()){
                                    $scope.Collection.Item.Index = collection.length-1;
                                }                            
                            }
                            else if (image.title.toLowerCase() === $scope.Collection.Item.Name.toLowerCase()){
                                $scope.Collection.Item.Index = -1;
                            }
                            
                        });
                    }
                });
            break;
            case 1:
                if ($scope.Collection.SubCategory.Name == ''){
                    $scope.Collection.SubCategory.Name = $scope.AllData[_themeIndex]['sub-items'][0]['sub-items'][0].name;
                }

                angular.forEach($scope.AllData[_themeIndex]['sub-items'], function(category, categoryIndex) {
                    if (category.name.toLowerCase() === $scope.Collection.Category.Name.toLowerCase()){
                        $scope.Collection.Category.Index = categoryIndex;     
                        angular.forEach(category['sub-items'], function(subCategory, subCategoryIndex) {
                            if(subCategory.name.toLowerCase() === $scope.Collection.SubCategory.Name.toLowerCase()){
                                $scope.Collection.SubCategory.Index = subCategoryIndex;     
                                angular.forEach(subCategory['videos'], function(video, videoIndex) {  ;                              
                                    
                                    if(video.isActive){
                                        collection.push(video);

                                        if(video.title.toLowerCase() === $scope.Collection.Item.Name.toLowerCase())
                                            $scope.Collection.Item.Index = collection.length-1;                                               
                                    }
                                    else if (image.title.toLowerCase() === $scope.Collection.Item.Name.toLowerCase()){
                                        $scope.Collection.Item.Index = -1;
                                    }

                                });
                            }
                        });
                    }
                });
            break;
        }

        $scope.Collection.Theme = $scope.Theme;
        $scope.Collection.Initialized = true;
        return collection;        

    }

    $scope.reloadVideos = function(src){
        $scope.ActiveItem.src = src;
    }

    $scope.carousel = function(isNext){
        
        var _currentIndex = $scope.Collection.Item.Index;
        var _dataCount = $scope.Collection.Data.length;
        var _newIndex = 0;

        if(isNext){
            if((_currentIndex + 1) < _dataCount)
                _newIndex  = _currentIndex += 1;
        }
        else
        {
            if((_currentIndex - 1) >= 0)
                _newIndex = _currentIndex -= 1;
            else
                _newIndex = _dataCount-1;            
        }
        $scope.Collection.Item.Index = _newIndex;
        $scope.RenderView();
        $location.path('/'+$scope.Collection.Theme.toLowerCase()+'/' + $scope.Collection.Category.Name + '/' + $scope.Collection.Data[$scope.Collection.Item.Index].title);

        analytics.pageClick('Carousel Click', $scope.Collection.Category.Name + ' - ' + $scope.Collection.Data[_newIndex].title);
        //$location.path('/'+$scope.theme.toLowerCase()+'/' + $scope.catName + '/' + $scope.dataCollection[_newIndex].title);
    } 

    if($scope.init){        
        $scope.Collection.Data = $scope.FillCollection();
        $scope.RenderView();
    }

}).controller('previewCtrl',function($scope){

    angular.forEach($scope.AllData, function(theme, index){
        if(theme.name.toLowerCase() === 'preview'){
            $scope.ActiveItem = theme.featuredVideos[0];
        }
    })

})