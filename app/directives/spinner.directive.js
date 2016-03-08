app.directive("lrSpinner", lrSpinner);

function lrSpinner(){
	return {
		restrict: 'E',
		transclude: false,
		scope: {
			isVideo: '=',
			errorText: '@lrErrorText'
		},
		controller: lrSpinnerCtrl,
		controllerAs: 'ctrl',
		template:
			'<div id="loadingScreen" >' +
    			'<div id="loadingBg"></div>' + 
    			'<div id="loadingContainer"  ng-class="{images: !ctrl.m.isVideo}">' +
      				'<div class="loader">Loading...</div>' +
    			'</div>' + 
  			'</div>',
		link: function linkFunction(scope, elm, attr) {			
			var loadContainer = elm[0].lastChild,
			loadContainerHeight = loadContainer.clientHeight,
			loadContainer = angular.element(loadContainer);
			loadContainer.css('marginTop', ((elm[0].parentElement.clientHeight - loadContainerHeight) / 2) + 'px')		
		},
		replace: true
	};
}

lrSpinnerCtrl.$inject = ['$scope'];

function lrSpinnerCtrl($scope) {
	var self = this;
	self.m = $scope;
}