app.directive('lrVideoBuffer', ['$interval', '$timeout', function($interval,$timeout) {
    return {
        restrict: 'A',
        link: function(scope, elm, attr) {
                var loadAttempts = 0;
                var reloadAttempts = 0;                
                var timeout;

                elm = angular.element(elm[0]);

                scope.videoTryLoad = function(){
                    scope.$parent.ActiveItem.isLoaded = false;
                    scope.ctrl.m.isFailed = false;
                    scope.ctrl.m.isAllowRetry = true;
                    interval = $interval(function() {

                        scope.readyState = elm[0].readyState;   

                        //video is ready
                        if (scope.readyState > 1){
                            scope.$parent.ActiveItem.isLoaded = true;
                            timeout = $timeout(function(){
                                //force the video to start playing                                
                                elm[0].play();
                            },1000)
                            $interval.cancel(interval);
                        }
                        else if(loadAttempts === 60){ // give it 5 seconds and then try again     
                            $interval.cancel(interval);  
                            elm[0].src = scope.$parent.ActiveItem.src;
                            scope.videoTryLoad();
                        }   
                        else if (loadAttempts >= 120){
                            $interval.cancel(interval);     
                            loadAttempts = 0;
                            reloadAttempts++;            
                            scope.$parent.ActiveItem.isLoaded = true;      
                            scope.ctrl.m.isFailed = true;

                            if(reloadAttempts > 1){
                                scope.ctrl.m.isAllowRetry = false;
                            }                         
                        }

                        loadAttempts ++;          
                    }, 50);

                }

                scope.videoTryLoad();

            elm.on('$destroy', function() {
                $interval.cancel(interval);
                $timeout.cancel(timeout);
                elm[0].pause();
                elm[0].remove();       
          });
        }
    }
}])