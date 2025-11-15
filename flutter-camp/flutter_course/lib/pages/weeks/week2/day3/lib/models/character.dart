class Character{
  int? id;
  String? name;
  String? imageUrl;

  Character({this.id,this.name,this.imageUrl});

  factory Character.fromJson(Map json){
    return Character(id:json["_id"],name: json["name"],imageUrl: json["imageUrl"] );
  }
}