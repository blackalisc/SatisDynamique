var app = angular.module('satisdynamique', ['ngResource', 'ui.bootstrap', 'xeditable']);

app.run(function(editableOptions) {
  editableOptions.theme = 'bs3';
});

app.factory('SatisDynamique', ['$resource',
    function($resource) {
        var myService = {
        allPakage: function() {
            return $resource('http://localhost/SatisDynamique/pakages');
        },
        postPakage: function() {
            return $resource('http://localhost/SatisDynamique/pakage');
        },
        allRepositories: function() {
            return $resource('http://localhost/SatisDynamique/repositories');
        },
        postRepository: function() {
            return $resource('http://localhost/SatisDynamique/repository');
        }
    };
    return myService;
    }
]);

/* take an array with flat representation of an object like that
     * data = [
     *  {"repository.type" : "package"},
     *  {"repository.package.name" : "angularjs"},
     *  {"repository.package.version" : "2.0"},
     * ]
     *  and return an object like that :
     *  {repository:
     *      type: "package",
     *      package: {
     *          "name" : "angularjs",
     *          "version" : "2.0"
     *          }
     *  }
     */
app.recomposeJsonFromFlat = function(data) {
        result = {};
        for (groupStr in data) {
            keys = groupStr.split(".");
            for(var i = keys.length; i--;) {
                if(i == keys.length - 1) {
                    group = {};
                    group[keys[i]] = data[groupStr];
                } else {
                    tmp = group;
                    group = {};
                    group[keys[i]] = tmp;
                }
            }
           jQuery().extend(true, result, result, group); 
        }
        return result;
};