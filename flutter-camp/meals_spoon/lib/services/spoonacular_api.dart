import 'dart:convert';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:http/http.dart' as http;
import 'package:meals_spoon/data/global_data.dart';
import 'package:meals_spoon/models/meal.dart';

class SpoonacularApi {
  final String apiKey = dotenv.env["ApiKey"]!;
  String get link =>
      "https://api.spoonacular.com/recipes/complexSearch?query=pasta&maxFat=25&number=20&apiKey=$apiKey";

  getData() async {
    allMeals.clear();
    var uri = Uri.parse(link);
    var response = await http.get(uri);

    var mealsJson = json.decode(response.body);

    for (var i = 0; i < mealsJson["results"].length; i++) {
      if (i == 1) continue;
      allMeals.add(Meal.fromJson(mealsJson["results"][i]));
    }
  }
}
