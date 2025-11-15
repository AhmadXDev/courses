class Meal {
  String? title;
  String? image;

  Meal({this.title, this.image});

  factory Meal.fromJson(Map<String, dynamic> json) {
    return Meal(title: json["title"], image: json["image"]);
  }
}
