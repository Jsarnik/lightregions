app.directive('lrPhotoImage', ['$timeout', '$window', function($timeout, $window) {
    return {
        restrict: 'A',
        link: function(scope, elm, attr) {

            var height = 700;
            elm = angular.element(elm[0]);

            elm[0].onload = function(){         
                var timeout = $timeout(function(){
                    if($(window).width() > 680){
                        elm.css('marginTop', ((700 - elm[0].height) / 2) + 'px');
                    }
                    scope.ActiveItem.isFullScreen = false;
                    scope.ActiveItem.isLoaded = true;
                },100)  
            }
        }
    }
}]);