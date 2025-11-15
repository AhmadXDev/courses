import '../data/global_data.dart';
import '../extension/push.dart';
import '../pages/information_page.dart';
import '../services/api.dart';
import '../widgets/text_widget.dart';
import 'package:flutter/material.dart';

class HomePage extends StatelessWidget {
  const HomePage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.blue.shade400,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: FutureBuilder(
            future: Api().getData(),
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return const Center(child: CircularProgressIndicator());
              } else if (snapshot.connectionState == ConnectionState.done) {
                return ListView.builder(
                  itemCount: allCharacters.length,
                  itemBuilder: (context, index) => InkWell(
                    onTap: () {
                      context.push(
                          InformationPage(character: allCharacters[index]));
                    },
                    child: Card(
                      color: index.isEven
                          ? Colors.blue.shade100
                          : Colors.pink.shade200,
                      child: Column(
                        children: [
                          TextWidget(
                            name: allCharacters[index].id.toString(),
                            num: index,
                          ),
                          const SizedBox(height: 12),
                          Image.network(
                              allCharacters[index].imageUrl.toString()),
                          const SizedBox(height: 12),
                          TextWidget(
                            name: allCharacters[index].name!,
                            isBold: true,
                            num: index,
                          )
                        ],
                      ),
                    ),
                  ),
                );
              } else {
                return const TextWidget(name: "No data", num: 0);
              }
            }),
      ),
    );
  }
}
