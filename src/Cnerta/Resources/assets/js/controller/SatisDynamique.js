app.controller('SatisDynamiqueCtrl', function($scope, $modal, SatisDynamique) {
    
    $scope.packages = [];
    $scope.currentPackageEdited = [];
    
    SatisDynamique.allPakage().get().$promise.then(function(p){
        $scope.packages = p.packages;
    });
    
    $scope.updatePackage = function(package) {        
        SatisDynamique.postPakage().save({package:{old:$scope.currentPackageEdited, new:package}});
    };
    
    $scope.onCancelEdit = function() {
        $scope.currentPackageEdited = null;
    };
    
    $scope.onShowEdit = function(package) {
        $scope.currentPackageEdited = {name: package.name, version: package.version};
    };
    
    $scope.addNewPackage = function() {
        $scope.packages.unshift({name: "New package", version: "*"});
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
                SatisDynamique.postPakage().remove({package:package});
            }
        });
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



