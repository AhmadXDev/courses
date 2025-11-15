class Meal {
  String? name;
  String? image;
  String? id;

  Meal({this.name, this.image, this.id});

  factory Meal.fromJson(Map<String, dynamic> json) {
    return Meal(
        name: json["strMeal"], image: json["strMealThumb"], id: json["idMeal"]);
  }
}
