import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map;
import java.util.Set;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

public class linkExtract {

	public static void main(String[] args) throws Exception {
		// TODO Auto-generated method stub
		String csvFile = "/home/preethishp/Documents/FoxNewsABCNewsData/mapFoxNewsFile.csv";
		//String csvFile = "mapABCNewsFile.csv";
        String line = "";
        String cvsSplitBy = ",";
        String dirpath = "/home/preethishp/Documents/solr-6.2.1/server/solr/NewsWebsites";
        //String dirpath = "NewsWebsites";
        File dir = new File(dirpath);
        File fileedge = new File("edgeList.txt");
        FileWriter writer = new FileWriter(fileedge.getAbsoluteFile());
        BufferedWriter bw = new BufferedWriter(writer);
        Set<String> edges = new HashSet<String>();
        
        Map<String, String> fileUrlMap = new HashMap<String,String>();
        Map<String, String> urlFileMap = new HashMap<String,String>();
        //System.out.println("print this");
        //int i=0;
        try (BufferedReader br = new BufferedReader(new FileReader(csvFile))) {
        	
            while ((line = br.readLine()) != null) {
            
                // use comma as separator
                String[] data = line.split(cvsSplitBy);
                //i++;
                //System.out.println(data[0]);
                fileUrlMap.put(data[0], data[1]);
                urlFileMap.put(data[1], data[0]);
            }
            //System.out.println(i);

        } catch (IOException e) {
            e.printStackTrace();
        }
     
      //System.out.println("reach this");
      
        for(File file: dir.listFiles())
        {
        	//System.out.println("Before Parse");
        	//System.out.println(file);
        	Document doc = Jsoup.parse(file, "UTF-8","http://www.foxnews.com/");
        	//System.out.println("After Parse");
        	
        	//System.out.println(fileUrlMap.get(file.getName()));
        	Elements links = doc.select("a[href]");
        	
        	for(Element link: links)
        	{
        		//System.out.println(link);
        		String url = link.attr("abs:href").trim();
        	
        		
        		if(urlFileMap.containsKey(url))
        		{
        			String path = file.getParent();
        			edges.add(file + " " + path+"/"+urlFileMap.get(url));
        		}
        	}
        	
        	
        }
        
        for(String s: edges)
        {
        	//System.out.println(s);
        	bw.write(s);
        	bw.newLine();
        }
        bw.close();
        writer.close();
	}

}
