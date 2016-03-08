app.controller("videoPlayerCtrl", videoPlayerCtrl)
.directive("lrVideoPlayer", videoPlayer);


videoPlayerCtrl.$inject = ['$scope'];

function videoPlayer() {
	return {
		restrict: 'E',
		scope: {
			src: '@lrSrc',
			isMobile: '='
		},
		replace: true,
		controller: videoPlayerCtrl,
		controllerAs: 'ctrl',
		template: 
		'<div id="video">' +
			'<div ng-if="ctrl.m.isFailed && !ctrl.m.isMobile" style="position:absolute; width:100%; text-align:center; padding-top:10%; z-index:9999; color: #fff;">' + 
  				'<a ng-if="ctrl.m.isAllowRetry" ng-click="videoTryLoad(ctrl.m.src)" style="color:#02F773; text-decoration:underline; cursor:pointer;" class="">RELOAD</a>' + 
  				'<span ng-if="!ctrl.m.isAllowRetry">Video failed to load.</span>' + 
			'</div>' + 
			'<video id="video-file" class="video-file" controls lr-video-buffer>' +
				'<source src="{{ctrl.m.src}}" type="video/mp4">' +
				'<source src="{{ctrl.m.src}}" type="video/ogg">Your browser does not support the video tag.' +
			'</video>'  +
			'<!--<iframe id="embed-video" class="embed-video" ng-src="{{ctrl.m.src}}" frameborder="0" style="width:100%; height:700px;" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>-->'  +
		'</div>',
		link: linkFunction
	};
}


function videoPlayerCtrl($scope) {	
	var self = this;
	self.m = {};
	self.m.src = $scope.src;
	self.m.isMobile = $scope.isMobile;
}

function linkFunction(scope, elem,attrs,ctrl) {

}

