class Category {
  String? id;
  String? name;
  String? image;
  String? description;

  Category({this.id, this.name, this.image, this.description});

  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
        id: json["idCategory"],
        name: json["strCategory"],
        image: json["strCategoryThumb"],
        description: json["strCategoryDescription"]);
  }
}
