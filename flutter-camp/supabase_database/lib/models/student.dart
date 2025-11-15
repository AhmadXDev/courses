class Student {
  int? id;
  String? name;
  int? age;
  double? salary;

  Student({this.name, this.age, this.salary, this.id});

  factory Student.fromJson(Map json) {
    return Student(
        age: json["age"],
        name: json["name"],
        salary: json["salary"],
        id: json["id"]);
  }
}
