import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:meals_list/data/global_data.dart';
import 'package:meals_list/models/category.dart';

class CategoriesApi {
  String link = "https://www.themealdb.com/api/json/v1/1/categories.php";

  getData() async {
    allCategories.clear();
    var uri = Uri.parse(link);
    var response = await http.get(uri);

    var categoriesJson = json.decode(response.body);

    for (var category in categoriesJson["categories"]) {
      allCategories.add(Category.fromJson(category));
    }
  }
}
