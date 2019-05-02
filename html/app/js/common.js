function getParam(name){
var params=location.search.substring(1);
var paramList=[];
var param=null;
var parami;
if(params.length>0) {
if(params.indexOf("&") >=0) {
   paramList=params.split( "&" ); 
}else {
   paramList[0] = params;
}
for(var i=0,listLength = paramList.length;i<listLength;i++) {
   parami = paramList[i].indexOf(name+"=" );
   if(parami>=0) {
    param =paramList[i].substr(parami+(name+"=").length);
   }
}
}
return param;
}