class Course {
  int? id;
  String? image;
  String? title;
  String? duration;
  String? level;
  List? tags;
  bool? prerequisite;
  double? rating;

  Course({
    this.id,
    this.tags,
    this.title,
    this.duration,
    this.image,
    this.level,
    this.prerequisite,
    this.rating,
  });

  factory Course.fromJson(Map<String,dynamic> json){
    return Course(
      id: json["id"],
      image: json["image"],
      title: json["title"],
      duration: json["duration"],
      level: json["level"],
      tags: json["tags"],
      prerequisite: json["prerequisite"],
      rating: json["rating"],
    );
  }
}
