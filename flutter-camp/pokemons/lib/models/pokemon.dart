class Pokemon {
  String? name;
  String? color;
  String? flavorText;
  String? imageUrl;
  List<String>? abilities;

  Pokemon({
    this.name,
    this.color,
    this.flavorText,
    this.imageUrl,
    this.abilities,
  });

  factory Pokemon.fromJson(Map<String, dynamic> json,
      {String? color,
      String? flavorText,
      String? imageUrl,
      List<String>? abilities}) {
    return Pokemon(
      name: json["name"],
      color: color,
      flavorText: flavorText,
      imageUrl: imageUrl,
      abilities: abilities,
    );
  }
}
