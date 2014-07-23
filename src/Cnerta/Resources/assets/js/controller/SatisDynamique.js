app.controller('SatisDynamiqueCtrl', function($scope, $modal, SatisDynamique) {
    
    $scope.packages = [];
    $scope.repositories = [];
    $scope.alerts = [];
    $scope.repositoryType = [
        {value: "package", text: 'package'},
        {value: "git", text: 'git'},
        {value: 'vcs', text: 'vcs'},
        {value: 'hg', text: 'hg'},
        {value: 'composer', text: 'composer'}
    ];
    $scope.repositoryInserted = [];
    
    $scope.loadPackage = function() {
        SatisDynamique.allPakage().get().$promise.then(function(p){
            $scope.packages = p.packages;
        },function(data, status, headers, config) {
            $scope.alerts.push({type:'danger', msg:angular.fromJson(data.data)});
        });
    }
    
    $scope.loadRepositories = function() {
        SatisDynamique.allRepositories().get().$promise.then(function(p){
            $scope.repositories = p.repositories;
        },function(data, status, headers, config) {
            $scope.alerts.push({type:'danger', msg:angular.fromJson(data.data)});
        });
    }
    
    $scope.loadPackage();
    $scope.loadRepositories();
        
    $scope.updatePackage = function(newPackage, oldPackage) {
        
        if(oldPackage.name == "" && oldPackage.version == "") {
            var package = {package:newPackage};
        } else {
            var package = {package:{old:oldPackage, new:newPackage}};
        }
        
        return SatisDynamique.postPakage()
                .save(package)
                .$promise.then(function(){
                    $scope.alerts.push({type:'success', msg:'The package have been well saved'});
                    $scope.loadPackage();
                    return true;
                }, function(data, status, headers, config) {                    
                    $scope.alerts.push({type:'danger', msg:angular.fromJson(data.data)});
                    return false;
                });
    };

    $scope.addNewPackage = function() {
        $scope.inserted = {
            name: '',
            version: ''
        };
        $scope.packages.unshift($scope.inserted);        
    };

    $scope.remove = function(index, package) {

        var modalInstance = $modal.open({
            templateUrl: 'removeModal.html',
            controller: ModalInstanceCtrl,
            resolve: {
                index: function() {
                    return index;
                },
                package: function() {
                    return package;
                }
            }
        });

        modalInstance.result.then(function(index) {
            if(index != undefined) {
                $scope.packages.splice(index, 1);
                
                SatisDynamique.postPakage().remove({package:package})
                .$promise.then(function(p){
                    $scope.alerts.push({type:'success', msg:'The package have been well removed'});
                    return true;
                }, function(data, status, headers, config) {                    
                    $scope.alerts.push({type:'danger', msg:angular.fromJson(data.data)});
                    return false;
                });
                
            }
        });
    };
    
        
    $scope.updateRepository = function(newRepository, oldRepository) {
        
//        if(oldPackage.name == "" && oldPackage.version == "") {
//            var package = {package:newPackage};
//        } else {
//            var package = {package:{old:oldPackage, new:newPackage}};
//        }
//        
//        return SatisDynamique.postPakage()
//                .save(package)
//                .$promise.then(function(){
//                    $scope.alerts.push({type:'success', msg:'The package have been well saved'});
//                    $scope.loadPackage();
//                    return true;
//                }, function(data, status, headers, config) {                    
//                    $scope.alerts.push({type:'danger', msg:angular.fromJson(data.data)});
//                    return false;
//                });
//        ;
    };
    
    $scope.addNewRepository = function() {
        $scope.repositoryInserted = {
            type: 'git',
            url: ''
        };
        $scope.repositories.unshift($scope.repositoryInserted);        
    };
    
    $scope.closeAlert = function(index) {
        $scope.alerts.splice(index, 1);
    };
});

var ModalInstanceCtrl = function($scope, $modalInstance, index, package) {

    $scope.item = package;

    $scope.ok = function() {
        $modalInstance.close(index);
    };

    $scope.cancel = function() {
        $modalInstance.close();
    };
};



