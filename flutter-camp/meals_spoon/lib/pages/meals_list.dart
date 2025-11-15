import 'package:flutter/material.dart';
import 'package:meals_spoon/data/global_data.dart';
import 'package:meals_spoon/services/spoonacular_api.dart';

class MealsListPage extends StatefulWidget {
  const MealsListPage({super.key});

  @override
  State<MealsListPage> createState() => _MealsListPageState();
}

class _MealsListPageState extends State<MealsListPage> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: AppBar(
          backgroundColor: Colors.green,
          title: const Text("Meals Page"),
          centerTitle: true,
        ),
        body: Padding(
            padding: const EdgeInsets.all(8),
            child: FutureBuilder(
                future: SpoonacularApi().getData(),
                builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.waiting) {
                    return const Center(
                      child: CircularProgressIndicator(),
                    );
                  } else if (snapshot.connectionState == ConnectionState.done) {
                    return ListView.builder(
                      itemCount: allMeals.length,
                      itemBuilder: (context, index) => Padding(
                          padding: const EdgeInsets.all(8.0),
                          child: Padding(
                            padding: const EdgeInsets.all(20),
                            child: Card(
                              child: Column(
                                children: [
                                  Image.network(
                                      allMeals[index].image.toString()),
                                  Text(allMeals[index].title.toString())
                                ],
                              ),
                            ),
                          )),
                    );
                  } else {
                    return const Center(
                      child: Text("No Data"),
                    );
                  }
                })));
  }
}
