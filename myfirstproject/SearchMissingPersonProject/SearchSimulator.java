import javax.swing.*;

public class SearchSimulator
{ 
    public static void main(String[] args) 
    {  
        JFrame frame = new JFrame("Missing People Search Simulation");  
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);    
        frame.getContentPane().add(new CityPanel());  
        frame.pack();  
        frame.setVisible(true);    
    }
}