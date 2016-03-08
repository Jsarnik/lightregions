app.controller("toXMLCtrl", function($scope, $http){       

    $scope.master = [];
    $scope.results = [];   
    $scope.theme = 'photography';

    $scope.get = function()
    {
        $current_theme_index = 0;

        switch ($scope.theme.toLowerCase()){
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

        $http.get('/data/data2.0.json').success(function(data){

            if(data.length < 1){
                $JSON = '';
                dataSvc.Save;
                //$scope.init(_theme);
            }

            $scope.master = data;
            $scope.results = data.menu[$current_theme_index];
            return;

            angular.forEach($scope.master['menu'][1], function(items, index){

                if (items['name'].toLowerCase() != $scope.theme)
                    return

                angular.forEach(items['sub-items'], function(subItems, index){

                if(subItems != null)                    
                    $scope.results.push(subItems);
                });
            }); 
        })

     
    }

    $scope.output = {};

    $scope.generateXML = function(){

        if($scope.theme.toLowerCase() === 'photography'){

            var stringbuild = '<?xml version="1.0" encoding="UTF-8"?>\r\n';
            stringbuild = stringbuild + '<categories>\r\n';
            stringbuild = stringbuild + '<category>\r\n<name>'+$scope.theme+'</name>\r\n';
            stringbuild = stringbuild + '<items>\r\n';

            angular.forEach($scope.results['sub-items'], function(item, key, index){

                stringbuild = stringbuild + '<item>\r\n';
                stringbuild = stringbuild + '<id>' + item.id + '</id>\r\n';
                stringbuild = stringbuild + '<name>' + item.name + '</name>\r\n';
                stringbuild = stringbuild + '<shortDesc>' + item.id + '</shortDesc>\r\n';
                stringbuild = stringbuild + '<images>\r\n';

                angular.forEach(item.images, function(image, key, index){
                    stringbuild = stringbuild + '<image>\r\n';
                    stringbuild = stringbuild + '<id>'+image.id+'</id>\r\n';
                    stringbuild = stringbuild + '<title>'+image.title+'</title>\r\n';
                    stringbuild = stringbuild + '<url>'+image.url+'</url>\r\n';
                    stringbuild = stringbuild + '<desc></desc>\r\n';
                    stringbuild = stringbuild + '<isActive>true</isActive>\r\n';
                    stringbuild = stringbuild + '</image>\r\n';
                })

                stringbuild = stringbuild + '</images>\r\n';
                stringbuild = stringbuild + '</item>\r\n';

            })

            stringbuild = stringbuild + '</items>\r\n';
            stringbuild = stringbuild + '</category>\r\n';
            stringbuild = stringbuild + '</categories>\r\n';

            $scope.output.data = stringbuild;

        }
        else {

            var stringbuild = '<?xml version="1.0" encoding="UTF-8"?>\r\n';
            stringbuild = stringbuild + '<'+ $scope.theme.toLowerCase()+'>\r\n';

            angular.forEach($scope.results['sub-items'], function(category, key, index){

                stringbuild = stringbuild + '<category>\r\n';
                stringbuild = stringbuild + '<id>'+category.id+'</id>\r\n';
                stringbuild = stringbuild + '<name>'+category.name+'</name>\r\n';
                stringbuild = stringbuild + '<shortDesc>'+category.shortDesc+'</shortDesc>\r\n';
                stringbuild = stringbuild + '<items>\r\n';

                angular.forEach(category['sub-items'], function(item, key, index){

                    stringbuild = stringbuild + '<item>\r\n';
                    stringbuild = stringbuild + '<id>' + item.id + '</id>\r\n';
                    stringbuild = stringbuild + '<name>' + item.name + '</name>\r\n';
                    stringbuild = stringbuild + '<shortDesc></shortDesc>\r\n';
                    stringbuild = stringbuild + '<videos>\r\n';


                    angular.forEach(item.videos, function(video, key, index){
                        stringbuild = stringbuild + '<video>\r\n';
                        stringbuild = stringbuild + '<id>'+video.id+'</id>\r\n';
                        stringbuild = stringbuild + '<title>'+video.title+'</title>\r\n';
                        stringbuild = stringbuild + '<thumb>'+video.thumb+'</thumb>\r\n';
                        stringbuild = stringbuild + '<src>'+video.src+'</src>\r\n';
                        stringbuild = stringbuild + '<vimeo>'+video.vimeo+'</vimeo>\r\n';
                        stringbuild = stringbuild + '<youtube>'+video.youtubeID+'</youtube>\r\n';
                        stringbuild = stringbuild + '<desc></desc>\r\n';
                        stringbuild = stringbuild + '<isActive>true</isActive>\r\n';
                        stringbuild = stringbuild + '</video>\r\n';
                    })

                    stringbuild = stringbuild + '</videos>\r\n';
                    stringbuild = stringbuild + '</item>\r\n';
                })
                
                stringbuild = stringbuild + '</items>\r\n';
                stringbuild = stringbuild + '</category>\r\n';
            })

            stringbuild = stringbuild + '</'+ $scope.theme.toLowerCase()+'>\r\n';

            $scope.output.data = stringbuild;           
        }        

    };

    $scope.isActive = '';
    $scope.generateJSON = function(){

        if($scope.theme === 'production'){
            angular.forEach($scope.results['sub-items'], function(category, key, index){

                angular.forEach(category['sub-items'], function(item, key, index){

                    angular.forEach(item.videos, function(video, key, index){
                        video.isActive = true;
                        console.log(video);
                    })

                })

            })
        }
        else{
            angular.forEach($scope.results['sub-items'], function(item, key, index){
                angular.forEach(item.images, function(image, key, index){
                    image.isActive = true;
                    console.log(image);
                })

            })
        }

        $scope.output.data = 'Success';
        $scope.save();
    }

    $scope.save = function(){   
            data = {
                'data' : $JSON,
                'path' : '/data/data2.0.json'
            };

           $http.post('save.php', data)
            .success(function(data, status, headers, config)
            {
                console.log(status + ' - ' + data);
            })
            .error(function(data, status, headers, config)
            {
                console.log('error');
            });

    }

        $scope.$watch('master', function(newVal){     
         $JSON = $scope.master;
    }, true);

})