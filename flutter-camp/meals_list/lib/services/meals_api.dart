import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:meals_list/data/global_data.dart';
import 'package:meals_list/models/meal.dart';

class MealsApi {
  String accessLink = "https://www.themealdb.com/api/json/v1/1/filter.php?c=";

  getData(String category) async {
    allMeals.clear();
    String link = accessLink + Uri.encodeComponent(category);
    var uri = Uri.parse(link);
    var response = await http.get(uri);

    var mealsJson = json.decode(response.body);

    for (var meal in mealsJson["meals"]) {
      allMeals.add(Meal.fromJson(meal));
    }
  }
}
