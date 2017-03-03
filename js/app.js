(function() {  
  var app = angular.module('wordalia', []);
  //Create a service or http calls
  app.factory('WDData', function($http){
    return {
        get : function(paramDate){
            return $http.get(baseURL+'be/index.php/wordalia/word/pDate/' + paramDate );
        }
    }
  });
  //Call the service inside the controller
  
  app.controller('WDController', function(WDData){
	var wdVault = this;
	wdVault.wd = []	
	tempDate.setDate(tempDate.getDate());
	// tempDate.setDate(tempDate.getDate() -8);
	// WDData.get(tempDate.getFullYear().toString() + '-'+(tempDate.getMonth()+1).toString() + '-'+(tempDate.getDate()-1).toString())
	WDData.get(tempDate.getFullYear().toString() + '-'+(tempDate.getMonth()+1).toString() + '-'+(tempDate.getDate()).toString())
	.then(function(data){
		// console.log(window.location.href+'be/index.php/wordalia/word');
        console.log('DId it work?' + JSON.stringify(data.data));
		wdVault.wd = data.data.word;
		// console.log('wdvault '+data.data.word);
    })
  });
  
  
  /*
  app.controller('WDController', function($http){
	var wdVault = this;
	wdVault.wd = []	
    // $http.get('http://localhost/wordalia/be/index.php/wordalia/word') //LOcal
    $http.get(window.location.href+'be/index.php/wordalia/word', { pDate: tempDate.getFullYear().toString() + '-'+(tempDate.getMonth()+1).toString() + '-'+(tempDate.getDate()-1).toString()}) //Azure
		.success(function(data) {
			wdVault.wd = data.word;
		});
  });
  */
})();

function getWord(val){
	tempDate.setDate(tempDate.getDate() + val);
	
	WDData.get(tempDate.getFullYear().toString() + '-'+(tempDate.getMonth()+1).toString() + '-'+(tempDate.getDate()-1).toString())
	.then(function(data){
		// console.log(window.location.href+'be/index.php/wordalia/word');
        console.log('DId it work?' + JSON.stringify(data.data));
		wdVault.wd = data.data.word;
		// console.log('wdvault '+data.data.word);
    });
	
	// $http.get(window.location.href+'be/index.php/wordalia/word', { pDate: tempDate.getFullYear().toString() + '-'+(tempDate.getMonth()+1).toString() + '-'+tempDate.getDate().toString()})
		// .success(function(data) {
			// console.log('Inside getWord' + JSON.stringify(data.data));
			// console.log();
		// });
  };

