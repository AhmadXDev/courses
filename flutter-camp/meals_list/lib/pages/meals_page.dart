import 'package:flutter/material.dart';
import 'package:meals_list/data/global_data.dart';
import 'package:meals_list/services/meals_api.dart';
import 'package:meals_list/widgets/text_widget.dart';

class MealsPage extends StatelessWidget {
  final String meal;
  const MealsPage({super.key, required this.meal});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
          backgroundColor: Colors.blue, title: Center(child: Text(meal))),
      body: Padding(
        padding: const EdgeInsets.all(8.0),
        child: FutureBuilder(
            future: MealsApi().getData(meal),
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return const Center(child: CircularProgressIndicator());
              } else if (snapshot.connectionState == ConnectionState.done) {
                return ListView.builder(
                    itemCount: allMeals.length,
                    itemBuilder: (context, index) => Padding(
                        padding: const EdgeInsets.all(8.0),
                        child: InkWell(
                          onTap: () {},
                          child: Card(
                            child: Padding(
                                padding: const EdgeInsets.all(8.0),
                                child: Center(
                                  child: Column(
                                    children: [
                                      Image.network(
                                        allMeals[index].image.toString(),
                                        width: 100,
                                        height: 100,
                                      ),
                                      const SizedBox(width: 20),
                                      Text(allMeals[index].name.toString())
                                    ],
                                  ),
                                )),
                          ),
                        )));
              } else {
                return const Center(
                    child: TextWidget(text: "No Data", size: 24));
              }
            }),
      ),
    );
  }
}
